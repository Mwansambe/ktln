package com.jamexams.app

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.compose.runtime.*
import androidx.navigation.compose.rememberNavController
import com.jamexams.app.data.local.TokenDataStore
import com.jamexams.app.navigation.AppNavHost
import com.jamexams.app.navigation.Routes
import com.jamexams.app.ui.theme.JamExamsTheme
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.runBlocking
import javax.inject.Inject

@AndroidEntryPoint
class MainActivity : ComponentActivity() {

    @Inject lateinit var tokenDataStore: TokenDataStore

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()

        // Determine start destination based on stored token
        val startDestination = runBlocking {
            if (tokenDataStore.token.first() != null) Routes.EXAMS else Routes.LOGIN
        }

        setContent {
            JamExamsTheme {
                val navController = rememberNavController()
                AppNavHost(
                    navController    = navController,
                    startDestination = startDestination,
                )
            }
        }
    }
}
