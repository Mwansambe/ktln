package com.jamexams.app.ui.components

import androidx.compose.foundation.layout.*
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.*
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp

/** Loading spinner displayed while data loads */
@Composable
fun LoadingView(message: String = "Loading...") {
    Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
        Column(horizontalAlignment = Alignment.CenterHorizontally) {
            CircularProgressIndicator(color = Color(0xFF1B5E20))
            Spacer(modifier = Modifier.height(12.dp))
            Text(message, color = Color.Gray, fontSize = 14.sp)
        }
    }
}

/** Error view with retry button */
@Composable
fun ErrorView(message: String, onRetry: () -> Unit) {
    Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
        Column(
            horizontalAlignment = Alignment.CenterHorizontally,
            modifier = Modifier.padding(32.dp)
        ) {
            Icon(Icons.Default.CloudOff, null,
                 modifier = Modifier.size(64.dp),
                 tint = Color.Gray)
            Spacer(modifier = Modifier.height(16.dp))
            Text("Something went wrong", fontWeight = FontWeight.SemiBold, fontSize = 18.sp)
            Spacer(modifier = Modifier.height(8.dp))
            Text(message, color = Color.Gray, textAlign = TextAlign.Center, fontSize = 14.sp)
            Spacer(modifier = Modifier.height(24.dp))
            Button(
                onClick = onRetry,
                colors  = ButtonDefaults.buttonColors(containerColor = Color(0xFF1B5E20))
            ) {
                Icon(Icons.Default.Refresh, null)
                Spacer(modifier = Modifier.width(8.dp))
                Text("Retry")
            }
        }
    }
}

/** Empty state view when no data available */
@Composable
fun EmptyView(message: String = "No items found", onClearFilter: (() -> Unit)? = null) {
    Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
        Column(
            horizontalAlignment = Alignment.CenterHorizontally,
            modifier = Modifier.padding(32.dp)
        ) {
            Icon(Icons.Default.SearchOff, null,
                 modifier = Modifier.size(64.dp),
                 tint = Color.Gray)
            Spacer(modifier = Modifier.height(16.dp))
            Text(message, fontWeight = FontWeight.SemiBold, fontSize = 18.sp)
            Spacer(modifier = Modifier.height(8.dp))
            Text("Try adjusting your filters", color = Color.Gray, fontSize = 14.sp)
            onClearFilter?.let {
                Spacer(modifier = Modifier.height(16.dp))
                OutlinedButton(onClick = it) { Text("Clear Filters") }
            }
        }
    }
}
