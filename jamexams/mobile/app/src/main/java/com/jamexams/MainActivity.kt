package com.jamexams.app

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.compose.runtime.*
import androidx.navigation.compose.rememberNavController
import com.jamexams.app.data.local.TokenDataStore
import com.jamexams.app.navigation.JamExamsNavGraph
import com.jamexams.app.navigation.Screen
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

        // Determine start destination based on saved token
        val isLoggedIn = runBlocking { tokenDataStore.token.first() != null }
        val startDestination = if (isLoggedIn) Screen.Home.route else Screen.Login.route

        setContent {
            JamExamsTheme {
                val navController = rememberNavController()
                JamExamsNavGraph(
                    navController      = navController,
                    startDestination   = startDestination,
                )
            }
        }
    }
}
