package com.jamexams.app.presentation.exams

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.jamexams.app.domain.model.*
import com.jamexams.app.domain.repository.ExamRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch
import javax.inject.Inject

/**
 * ExamListViewModel
 * Manages exam list with pagination, filtering, and search.
 */
@HiltViewModel
class ExamListViewModel @Inject constructor(
    private val examRepository: ExamRepository,
) : ViewModel() {

    private val _uiState = MutableStateFlow<ExamListUiState>(ExamListUiState.Loading)
    val uiState: StateFlow<ExamListUiState> = _uiState.asStateFlow()

    private val _filter = MutableStateFlow(ExamFilter())
    val filter: StateFlow<ExamFilter> = _filter.asStateFlow()

    private val _searchQuery = MutableStateFlow("")
    val searchQuery: StateFlow<String> = _searchQuery.asStateFlow()

    private var currentPage = 1
    private var hasMore = true
    private val loadedExams = mutableListOf<Exam>()

    init { loadExams(reset = true) }

    fun onSearchQueryChanged(query: String) {
        _searchQuery.value = query
        _filter.value = _filter.value.copy(search = query)
        loadExams(reset = true)
    }

    fun onFilterChanged(filter: ExamFilter) {
        _filter.value = filter
        loadExams(reset = true)
    }

    fun loadMoreExams() {
        if (hasMore && _uiState.value !is ExamListUiState.LoadingMore) {
            loadExams(reset = false)
        }
    }

    fun retry() { loadExams(reset = true) }

    fun refresh() { loadExams(reset = true) }

    private fun loadExams(reset: Boolean) {
        viewModelScope.launch {
            if (reset) {
                currentPage = 1
                loadedExams.clear()
                _uiState.value = ExamListUiState.Loading
            } else {
                _uiState.value = ExamListUiState.LoadingMore(loadedExams.toList())
            }

            when (val result = examRepository.getExams(_filter.value, currentPage)) {
                is Result.Success -> {
                    val (newExams, moreAvailable) = result.data
                    loadedExams.addAll(newExams)
                    hasMore = moreAvailable
                    if (reset) currentPage = 1
                    currentPage++

                    _uiState.value = if (loadedExams.isEmpty()) {
                        ExamListUiState.Empty
                    } else {
                        ExamListUiState.Success(
                            exams   = loadedExams.toList(),
                            hasMore = hasMore,
                        )
                    }
                }
                is Result.Error -> {
                    _uiState.value = if (loadedExams.isEmpty()) {
                        ExamListUiState.Error(result.message)
                    } else {
                        ExamListUiState.Success(loadedExams.toList(), hasMore)
                    }
                }
                is Result.Loading -> {}
            }
        }
    }
}

sealed class ExamListUiState {
    object Loading : ExamListUiState()
    object Empty   : ExamListUiState()
    data class Success(val exams: List<Exam>, val hasMore: Boolean = false) : ExamListUiState()
    data class LoadingMore(val exams: List<Exam>) : ExamListUiState()
    data class Error(val message: String) : ExamListUiState()
}
