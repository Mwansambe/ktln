package com.jamexams.app.ui.theme

import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.graphics.Color

private val JamColorScheme = lightColorScheme(
    primary          = Color(0xFF1DB954),  // JamExams green
    onPrimary        = Color.White,
    primaryContainer = Color(0xFFD8F5E4),
    secondary        = Color(0xFFE53E3E),  // JamExams red
    onSecondary      = Color.White,
    background       = Color(0xFFF8F9FA),
    surface          = Color.White,
    onBackground     = Color(0xFF1A1A2E),
    onSurface        = Color(0xFF1A1A2E),
)

@Composable
fun JamExamsTheme(content: @Composable () -> Unit) {
    MaterialTheme(
        colorScheme = JamColorScheme,
        typography  = Typography(),
        content     = content,
    )
}
