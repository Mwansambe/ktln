package com.jamexams.app.domain.model

/**
 * Domain Models - Pure Kotlin data classes for business logic.
 * These are decoupled from the API/data layer.
 * The repository maps data models → domain models.
 */

data class User(
    val id:            String,
    val name:          String,
    val email:         String,
    val phone:         String?,
    val avatar:        String?,
    val isActive:      Boolean,
    val activatedAt:   String?,
    val expiresAt:     String?,
    val daysRemaining: Int,
    val roles:         List<String>,
)

data class Subject(
    val id:          Int,
    val name:        String,
    val code:        String,
    val description: String?,
    val icon:        String?,
    val color:       String?,
    val examCount:   Int,
)

data class Exam(
    val id:               Int,
    val code:             String,
    val title:            String,
    val description:      String?,
    val examType:         String,
    val classLevel:       String?,
    val year:             Int?,
    val hasMarkingScheme: Boolean,
    val examFileSize:     Long,
    val markingSchemeSize:Long,
    val isFeatured:       Boolean,
    val downloadCount:    Int,
    val subject:          Subject?,
    val createdAt:        String,
)

data class ExamFilter(
    val subjectId:  Int?    = null,
    val classLevel: String? = null,
    val examType:   String? = null,
    val year:       Int?    = null,
    val search:     String? = null,
    val featured:   Boolean? = null,
)

// Result wrapper - used by repository and ViewModel
sealed class Result<out T> {
    data class Success<T>(val data: T) : Result<T>()
    data class Error(val message: String, val code: String? = null) : Result<Nothing>()
    object Loading : Result<Nothing>()
}
