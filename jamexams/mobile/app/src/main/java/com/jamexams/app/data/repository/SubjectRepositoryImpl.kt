package com.jamexams.app.data.repository

import com.jamexams.app.data.api.ApiService
import com.jamexams.app.domain.model.Result
import com.jamexams.app.domain.model.Subject
import com.jamexams.app.domain.repository.SubjectRepository
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.flowOf
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class SubjectRepositoryImpl @Inject constructor(private val api: ApiService) : SubjectRepository {

    override suspend fun getSubjects(): Result<List<Subject>> {
        return try {
            val response = api.getSubjects()
            if (response.isSuccessful && response.body()?.status == "success") {
                val subjects = response.body()!!.data!!.subjects.map {
                    Subject(id = it.id, name = it.name, code = it.code, description = it.description,
                        icon = it.icon, color = it.color, examCount = it.examCount)
                }
                Result.Success(subjects)
            } else {
                Result.Error(response.body()?.message ?: "Failed to load subjects")
            }
        } catch (e: Exception) {
            Result.Error(e.message ?: "Network error")
        }
    }

    override fun getCachedSubjects(): Flow<List<Subject>> = flowOf(emptyList())
}
