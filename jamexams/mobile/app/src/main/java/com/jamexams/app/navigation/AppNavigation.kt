package com.jamexams.app.navigation

import androidx.compose.runtime.*
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.navigation.NavHostController
import androidx.navigation.compose.*
import androidx.navigation.NavType
import androidx.navigation.navArgument
import com.jamexams.app.presentation.auth.LoginScreen
import com.jamexams.app.presentation.exams.ExamListScreen
import com.jamexams.app.presentation.subjects.SubjectListScreen

/** Navigation route constants */
object Routes {
    const val LOGIN    = "login"
    const val HOME     = "home"
    const val EXAMS    = "exams"
    const val SUBJECTS = "subjects"
    const val EXAM_DETAIL = "exam/{examId}"
    fun examDetail(id: Int) = "exam/$id"
}

@Composable
fun AppNavHost(navController: NavHostController, startDestination: String) {
    NavHost(navController = navController, startDestination = startDestination) {

        // Login screen (no bottom nav)
        composable(Routes.LOGIN) {
            LoginScreen(
                onLoginSuccess = {
                    navController.navigate(Routes.EXAMS) {
                        popUpTo(Routes.LOGIN) { inclusive = true }
                    }
                }
            )
        }

        // Subjects screen
        composable(Routes.SUBJECTS) {
            SubjectListScreen(
                onSubjectClick = { subject ->
                    navController.navigate(Routes.EXAMS + "?subjectId=${subject.id}")
                }
            )
        }

        // Exams list screen
        composable(
            route = Routes.EXAMS + "?subjectId={subjectId}",
            arguments = listOf(navArgument("subjectId") {
                type = NavType.StringType
                nullable = true
                defaultValue = null
            })
        ) {
            ExamListScreen(
                onExamClick = { exam ->
                    navController.navigate(Routes.examDetail(exam.id))
                }
            )
        }
    }
}
