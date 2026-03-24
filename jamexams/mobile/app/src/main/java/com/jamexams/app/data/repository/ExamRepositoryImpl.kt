package com.jamexams.app.data.repository

import android.content.Context
import com.jamexams.app.data.api.ApiService
import com.jamexams.app.domain.model.*
import com.jamexams.app.domain.repository.ExamRepository
import dagger.hilt.android.qualifiers.ApplicationContext
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import java.io.File
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class ExamRepositoryImpl @Inject constructor(
    private val api: ApiService,
    @ApplicationContext private val context: Context
) : ExamRepository {

    override suspend fun getSubjects(): Result<List<Subject>> {
        return try {
            val response = api.getSubjects()
            if (response.isSuccessful && response.body()?.status == "success") {
                val subjects = response.body()!!.data!!.subjects.map { it.toDomain() }
                Result.Success(subjects)
            } else {
                Result.Error(response.body()?.message ?: "Failed to load subjects")
            }
        } catch (e: Exception) {
            Result.Error(e.message ?: "Network error")
        }
    }

    override suspend fun getExams(filter: ExamFilter, page: Int): Result<Pair<List<Exam>, Int>> {
        return try {
            val response = api.getExams(
                subjectId  = filter.subjectId,
                classLevel = filter.classLevel,
                examType   = filter.examType,
                year       = filter.year,
                search     = filter.search,
                featured   = filter.featured,
                page       = page,
            )
            if (response.isSuccessful && response.body()?.status == "success") {
                val data   = response.body()!!.data!!
                val exams  = data.exams.map { it.toDomain() }
                val lastPage = data.pagination.lastPage
                Result.Success(Pair(exams, lastPage))
            } else {
                Result.Error(response.body()?.message ?: "Failed to load exams")
            }
        } catch (e: Exception) {
            Result.Error(e.message ?: "Network error")
        }
    }

    override suspend fun getExam(id: Int): Result<Exam> {
        return try {
            val response = api.getExam(id)
            if (response.isSuccessful && response.body()?.status == "success") {
                Result.Success(response.body()!!.data!!.exam.toDomain())
            } else {
                Result.Error(response.body()?.message ?: "Exam not found")
            }
        } catch (e: Exception) {
            Result.Error(e.message ?: "Network error")
        }
    }

    override suspend fun downloadExam(id: Int, fileName: String): Result<String> {
        return downloadFile(
            fetchBody = { api.downloadExam(id) },
            fileName  = "$fileName.pdf"
        )
    }

    override suspend fun downloadMarkingScheme(id: Int, fileName: String): Result<String> {
        return downloadFile(
            fetchBody = { api.downloadMarkingScheme(id) },
            fileName  = "${fileName}_marking.pdf"
        )
    }

    private suspend fun downloadFile(
        fetchBody: suspend () -> retrofit2.Response<okhttp3.ResponseBody>,
        fileName: String
    ): Result<String> = withContext(Dispatchers.IO) {
        try {
            val response = fetchBody()
            if (response.isSuccessful) {
                val downloadsDir = context.getExternalFilesDir("JamExams")
                    ?: return@withContext Result.Error("Storage not available")
                if (!downloadsDir.exists()) downloadsDir.mkdirs()

                val file = File(downloadsDir, fileName.replace("/", "_"))
                response.body()?.byteStream()?.use { input ->
                    file.outputStream().use { output ->
                        input.copyTo(output)
                    }
                }
                Result.Success(file.absolutePath)
            } else {
                Result.Error("Download failed: ${response.code()}")
            }
        } catch (e: Exception) {
            Result.Error(e.message ?: "Download failed")
        }
    }
}

// Extension mappers
fun com.jamexams.app.data.model.SubjectData.toDomain() = Subject(
    id = id, name = name, code = code,
    description = description, icon = icon, color = color, examCount = examCount
)

fun com.jamexams.app.data.model.ExamData.toDomain() = Exam(
    id = id, code = code, title = title, description = description,
    examType = examType, classLevel = classLevel, year = year,
    hasMarkingScheme = hasMarkingScheme, examFileSize = examFileSize,
    markingSchemeSize = markingSchemeSize, isFeatured = isFeatured,
    downloadCount = downloadCount, subject = subject?.toDomain(), createdAt = createdAt
)
