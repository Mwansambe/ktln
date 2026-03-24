package com.jamexams.app.presentation.exams

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.jamexams.app.domain.model.Exam
import com.jamexams.app.domain.model.Result
import com.jamexams.app.domain.repository.ExamRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class ExamDetailViewModel @Inject constructor(
    private val examRepository: ExamRepository,
) : ViewModel() {

    private val _uiState = MutableStateFlow<ExamDetailUiState>(ExamDetailUiState.Loading)
    val uiState: StateFlow<ExamDetailUiState> = _uiState.asStateFlow()

    private val _downloadState = MutableStateFlow<DownloadState>(DownloadState.Idle)
    val downloadState: StateFlow<DownloadState> = _downloadState.asStateFlow()

    private var currentExamId: Int = -1

    fun loadExam(id: Int) {
        currentExamId = id
        viewModelScope.launch {
            _uiState.value = ExamDetailUiState.Loading
            when (val result = examRepository.getExam(id)) {
                is Result.Success -> _uiState.value = ExamDetailUiState.Success(result.data)
                is Result.Error   -> _uiState.value = ExamDetailUiState.Error(result.message)
                is Result.Loading -> {}
            }
        }
    }

    fun downloadExam() {
        viewModelScope.launch {
            _downloadState.value = DownloadState.Downloading
            when (val result = examRepository.downloadExam(currentExamId)) {
                is Result.Success -> _downloadState.value = DownloadState.Success(result.data)
                is Result.Error   -> _downloadState.value = DownloadState.Error(result.message)
                is Result.Loading -> {}
            }
        }
    }

    fun downloadMarkingScheme() {
        viewModelScope.launch {
            _downloadState.value = DownloadState.Downloading
            when (val result = examRepository.downloadMarkingScheme(currentExamId)) {
                is Result.Success -> _downloadState.value = DownloadState.Success(result.data)
                is Result.Error   -> _downloadState.value = DownloadState.Error(result.message)
                is Result.Loading -> {}
            }
        }
    }

    sealed class DownloadState {
        object Idle       : DownloadState()
        object Downloading : DownloadState()
        data class Success(val body: okhttp3.ResponseBody) : DownloadState()
        data class Error(val message: String)              : DownloadState()
    }
}

sealed class ExamDetailUiState {
    object Loading                  : ExamDetailUiState()
    data class Success(val exam: Exam) : ExamDetailUiState()
    data class Error(val message: String) : ExamDetailUiState()
}
