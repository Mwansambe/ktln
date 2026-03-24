package com.jamexams.app.presentation.subjects

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.jamexams.app.domain.model.Result
import com.jamexams.app.domain.model.Subject
import com.jamexams.app.domain.repository.SubjectRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class SubjectListViewModel @Inject constructor(
    private val subjectRepository: SubjectRepository,
) : ViewModel() {

    private val _uiState = MutableStateFlow<SubjectListUiState>(SubjectListUiState.Loading)
    val uiState: StateFlow<SubjectListUiState> = _uiState.asStateFlow()

    init { load() }

    fun load() {
        viewModelScope.launch {
            _uiState.value = SubjectListUiState.Loading
            when (val result = subjectRepository.getSubjects()) {
                is Result.Success -> _uiState.value = if (result.data.isEmpty()) SubjectListUiState.Error("No subjects found")
                                     else SubjectListUiState.Success(result.data)
                is Result.Error   -> _uiState.value = SubjectListUiState.Error(result.message)
                is Result.Loading -> {}
            }
        }
    }
}

sealed class SubjectListUiState {
    object Loading : SubjectListUiState()
    data class Success(val subjects: List<Subject>) : SubjectListUiState()
    data class Error(val message: String)           : SubjectListUiState()
}
