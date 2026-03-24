package com.jamexams.app.presentation.exams

import android.content.Context
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.jamexams.app.domain.model.*
import com.jamexams.app.domain.repository.ExamRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch
import java.io.File
import javax.inject.Inject

data class ExamsUiState(
    val isLoading: Boolean          = false,
    val exams: List<Exam>           = emptyList(),
    val subjects: List<Subject>     = emptyList(),
    val filter: ExamFilter          = ExamFilter(),
    val errorMessage: String?       = null,
    val isEmpty: Boolean            = false
)

data class DownloadState(
    val examId: Int         = 0,
    val progress: Int       = 0,
    val isDownloading: Boolean = false,
    val isComplete: Boolean = false,
    val filePath: String?   = null,
    val error: String?      = null
)

/**
 * ExamsViewModel - Manages exams list, filtering, and PDF download.
 * Uses StateFlow for reactive UI state management.
 */
@HiltViewModel
class ExamsViewModel @Inject constructor(
    private val examRepository: ExamRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(ExamsUiState())
    val uiState: StateFlow<ExamsUiState> = _uiState.asStateFlow()

    private val _downloadState = MutableStateFlow(DownloadState())
    val downloadState: StateFlow<DownloadState> = _downloadState.asStateFlow()

    init {
        loadSubjects()
        loadExams()
    }

    fun loadSubjects() {
        viewModelScope.launch {
            when (val result = examRepository.getSubjects()) {
                is Result.Success -> _uiState.update { it.copy(subjects = result.data) }
                is Result.Error   -> _uiState.update { it.copy(errorMessage = result.message) }
                else -> Unit
            }
        }
    }

    fun loadExams(filter: ExamFilter = _uiState.value.filter) {
        viewModelScope.launch {
            _uiState.update { it.copy(isLoading = true, errorMessage = null) }

            when (val result = examRepository.getExams(filter, page = 1)) {
                is Result.Success -> {
                    _uiState.update {
                        it.copy(
                            isLoading    = false,
                            exams        = result.data,
                            filter       = filter,
                            isEmpty      = result.data.isEmpty()
                        )
                    }
                }
                is Result.Error -> {
                    _uiState.update {
                        it.copy(isLoading = false, errorMessage = result.message)
                    }
                }
                else -> Unit
            }
        }
    }

    fun applyFilter(filter: ExamFilter) = loadExams(filter)

    fun clearFilter() = loadExams(ExamFilter())

    fun downloadExam(context: Context, exam: Exam) {
        viewModelScope.launch {
            val destFile = File(context.getExternalFilesDir(null), "${exam.title}.pdf")
            _downloadState.value = DownloadState(examId = exam.id, isDownloading = true)

            when (val result = examRepository.downloadExam(exam.id, destFile) { progress ->
                _downloadState.update { it.copy(progress = progress) }
            }) {
                is Result.Success -> {
                    _downloadState.value = DownloadState(
                        examId     = exam.id,
                        isComplete = true,
                        filePath   = result.data.absolutePath
                    )
                }
                is Result.Error -> {
                    _downloadState.value = DownloadState(
                        examId = exam.id,
                        error  = result.message
                    )
                }
                else -> Unit
            }
        }
    }

    fun clearDownloadState() { _downloadState.value = DownloadState() }
}
