package com.jamexams.app.navigation

import androidx.compose.runtime.Composable
import androidx.navigation.NavHostController
import androidx.navigation.NavType
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.navArgument
import androidx.navigation.navDeepLink
import com.jamexams.app.ui.screens.ExamDetailScreen
import com.jamexams.app.ui.screens.ExamListScreen
import com.jamexams.app.ui.screens.HomeScreen
import com.jamexams.app.ui.screens.LoginScreen
import com.jamexams.app.ui.screens.SubjectListScreen

/**
 * Navigation graph for JamExams.
 * Routes defined as sealed class for type safety.
 */
sealed class Screen(val route: String) {
    object Login      : Screen("login")
    object Home       : Screen("home")
    object Subjects   : Screen("subjects")
    object ExamList   : Screen("exams?subjectId={subjectId}") {
        fun withSubject(id: Int) = "exams?subjectId=$id"
        fun all() = "exams"
    }
    object ExamDetail : Screen("exam/{examId}") {
        fun withId(id: Int) = "exam/$id"
    }
}

@Composable
fun JamExamsNavGraph(
    navController: NavHostController,
    startDestination: String = Screen.Login.route,
) {
    NavHost(navController = navController, startDestination = startDestination) {

        composable(Screen.Login.route) {
            LoginScreen(
                onLoginSuccess = {
                    navController.navigate(Screen.Home.route) {
                        popUpTo(Screen.Login.route) { inclusive = true }
                    }
                }
            )
        }

        composable(Screen.Home.route) {
            HomeScreen(
                onNavigateToExams    = { navController.navigate(Screen.ExamList.all()) },
                onNavigateToSubjects = { navController.navigate(Screen.Subjects.route) },
                onExamClick          = { id -> navController.navigate(Screen.ExamDetail.withId(id)) },
            )
        }

        composable(Screen.Subjects.route) {
            SubjectListScreen(
                onSubjectClick = { subjectId ->
                    navController.navigate(Screen.ExamList.withSubject(subjectId))
                }
            )
        }

        composable(
            route = "exams?subjectId={subjectId}",
            arguments = listOf(
                navArgument("subjectId") {
                    type = NavType.IntType
                    defaultValue = -1
                }
            ),
            // Deep link from FCM notification
            deepLinks = listOf(navDeepLink { uriPattern = "jamexams://exams/{examId}" })
        ) { backStackEntry ->
            val subjectId = backStackEntry.arguments?.getInt("subjectId")?.takeIf { it != -1 }
            ExamListScreen(
                onExamClick = { id -> navController.navigate(Screen.ExamDetail.withId(id)) },
            )
        }

        composable(
            route = Screen.ExamDetail.route,
            arguments = listOf(navArgument("examId") { type = NavType.IntType }),
            // Deep link: opens specific exam from notification tap
            deepLinks = listOf(navDeepLink { uriPattern = "jamexams://exam/{examId}" })
        ) { backStackEntry ->
            val examId = backStackEntry.arguments?.getInt("examId") ?: return@composable
            ExamDetailScreen(
                examId = examId,
                onBack = { navController.popBackStack() },
            )
        }
    }
}
