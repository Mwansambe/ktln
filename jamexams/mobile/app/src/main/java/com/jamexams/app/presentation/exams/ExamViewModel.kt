package com.jamexams.app.presentation.exams

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.jamexams.app.domain.model.*
import com.jamexams.app.domain.repository.ExamRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch
import javax.inject.Inject

// UI State sealed class
sealed class ExamUiState {
    object Idle    : ExamUiState()
    object Loading : ExamUiState()
    data class Success(val exams: List<Exam>, val hasMore: Boolean) : ExamUiState()
    data class Empty(val message: String = "No exams found") : ExamUiState()
    data class Error(val message: String) : ExamUiState()
}

sealed class DownloadState {
    object Idle         : DownloadState()
    object Downloading  : DownloadState()
    data class Done(val filePath: String) : DownloadState()
    data class Error(val message: String) : DownloadState()
}

/**
 * ExamViewModel
 * Manages exam listing with filtering, pagination, and download.
 * Uses MVVM + StateFlow for unidirectional data flow.
 */
@HiltViewModel
class ExamViewModel @Inject constructor(
    private val repository: ExamRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow<ExamUiState>(ExamUiState.Idle)
    val uiState: StateFlow<ExamUiState> = _uiState.asStateFlow()

    private val _downloadState = MutableStateFlow<DownloadState>(DownloadState.Idle)
    val downloadState: StateFlow<DownloadState> = _downloadState.asStateFlow()

    // Filter state
    private val _filter  = MutableStateFlow(ExamFilter())
    val filter: StateFlow<ExamFilter> = _filter.asStateFlow()

    private var currentPage  = 1
    private var lastPage     = 1
    private val loadedExams  = mutableListOf<Exam>()

    init { loadExams() }

    fun loadExams(reset: Boolean = true) {
        if (reset) {
            currentPage = 1
            loadedExams.clear()
        }
        if (currentPage > lastPage && !reset) return

        viewModelScope.launch {
            _uiState.value = if (reset) ExamUiState.Loading else _uiState.value

            when (val result = repository.getExams(_filter.value, currentPage)) {
                is Result.Success -> {
                    val (newExams, pages) = result.data
                    lastPage = pages
                    loadedExams.addAll(newExams)
                    currentPage++

                    _uiState.value = if (loadedExams.isEmpty())
                        ExamUiState.Empty()
                    else
                        ExamUiState.Success(loadedExams.toList(), currentPage <= lastPage)
                }
                is Result.Error -> {
                    _uiState.value = if (loadedExams.isEmpty())
                        ExamUiState.Error(result.message)
                    else
                        ExamUiState.Success(loadedExams.toList(), false) // Keep current, stop pagination
                }
                else -> {}
            }
        }
    }

    fun loadMore() {
        if (_uiState.value is ExamUiState.Success &&
            (_uiState.value as ExamUiState.Success).hasMore) {
            loadExams(reset = false)
        }
    }

    fun applyFilter(newFilter: ExamFilter) {
        _filter.value = newFilter
        loadExams(reset = true)
    }

    fun clearFilter() {
        _filter.value = ExamFilter()
        loadExams(reset = true)
    }

    fun downloadExam(exam: Exam) {
        viewModelScope.launch {
            _downloadState.value = DownloadState.Downloading
            when (val result = repository.downloadExam(exam.id, exam.title)) {
                is Result.Success -> _downloadState.value = DownloadState.Done(result.data)
                is Result.Error   -> _downloadState.value = DownloadState.Error(result.message)
                else -> {}
            }
        }
    }

    fun downloadMarkingScheme(exam: Exam) {
        viewModelScope.launch {
            _downloadState.value = DownloadState.Downloading
            when (val result = repository.downloadMarkingScheme(exam.id, exam.title)) {
                is Result.Success -> _downloadState.value = DownloadState.Done(result.data)
                is Result.Error   -> _downloadState.value = DownloadState.Error(result.message)
                else -> {}
            }
        }
    }

    fun resetDownloadState() {
        _downloadState.value = DownloadState.Idle
    }
}
