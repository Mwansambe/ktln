package com.jamexams.app.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.jamexams.app.presentation.exams.ExamListUiState
import com.jamexams.app.presentation.exams.ExamListViewModel
import com.jamexams.app.ui.theme.JamGreen
import com.jamexams.app.ui.theme.JamRed

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun HomeScreen(
    onNavigateToExams: () -> Unit,
    onNavigateToSubjects: () -> Unit,
    onExamClick: (Int) -> Unit,
    viewModel: ExamListViewModel = hiltViewModel(),
) {
    val uiState by viewModel.uiState.collectAsState()

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(Icons.Filled.MenuBook, null, tint = JamGreen, modifier = Modifier.size(28.dp))
                        Spacer(Modifier.width(8.dp))
                        Text("JamExams", fontWeight = FontWeight.Bold)
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = Color.White),
            )
        },
        bottomBar = {
            NavigationBar(containerColor = Color.White) {
                NavigationBarItem(selected = true, onClick = {}, icon = { Icon(Icons.Filled.Home, null) }, label = { Text("Home") })
                NavigationBarItem(selected = false, onClick = onNavigateToExams, icon = { Icon(Icons.Filled.Description, null) }, label = { Text("Exams") })
                NavigationBarItem(selected = false, onClick = onNavigateToSubjects, icon = { Icon(Icons.Filled.Category, null) }, label = { Text("Subjects") })
            }
        }
    ) { padding ->
        Column(
            Modifier.fillMaxSize().padding(padding).verticalScroll(rememberScrollState()).padding(horizontal = 16.dp)
        ) {
            Spacer(Modifier.height(16.dp))
            // Welcome banner
            Card(
                Modifier.fillMaxWidth(), shape = RoundedCornerShape(16.dp),
                colors = CardDefaults.cardColors(containerColor = JamGreen),
            ) {
                Column(Modifier.padding(20.dp)) {
                    Text("Welcome to JamExams 📚", color = Color.White, fontWeight = FontWeight.Bold, style = MaterialTheme.typography.titleMedium)
                    Spacer(Modifier.height(4.dp))
                    Text("Access your exam papers and marking schemes", color = Color.White.copy(alpha = 0.85f), style = MaterialTheme.typography.bodySmall)
                }
            }
            Spacer(Modifier.height(20.dp))

            // Quick actions
            Text("Quick Access", fontWeight = FontWeight.SemiBold, style = MaterialTheme.typography.titleSmall)
            Spacer(Modifier.height(10.dp))
            Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                QuickActionCard(Modifier.weight(1f), icon = Icons.Filled.Description, label = "All Exams", color = JamGreen, onClick = onNavigateToExams)
                QuickActionCard(Modifier.weight(1f), icon = Icons.Filled.Category, label = "Subjects", color = JamRed, onClick = onNavigateToSubjects)
            }
            Spacer(Modifier.height(20.dp))

            // Recent exams
            Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
                Text("Recent Exams", fontWeight = FontWeight.SemiBold, style = MaterialTheme.typography.titleSmall)
                TextButton(onClick = onNavigateToExams) { Text("View All", color = JamGreen) }
            }
            Spacer(Modifier.height(8.dp))

            when (val state = uiState) {
                is ExamListUiState.Success -> {
                    state.exams.take(5).forEach { exam ->
                        ExamCard(exam = exam, onClick = { onExamClick(exam.id) })
                        Spacer(Modifier.height(8.dp))
                    }
                }
                is ExamListUiState.Loading -> CircularProgressIndicator(color = JamGreen, modifier = Modifier.align(Alignment.CenterHorizontally))
                else -> {}
            }
            Spacer(Modifier.height(20.dp))
        }
    }
}

@Composable
private fun QuickActionCard(modifier: Modifier = Modifier, icon: androidx.compose.ui.graphics.vector.ImageVector, label: String, color: Color, onClick: () -> Unit) {
    Card(modifier = modifier, onClick = onClick, shape = RoundedCornerShape(14.dp),
        colors = CardDefaults.cardColors(containerColor = color.copy(alpha = 0.1f)),
        elevation = CardDefaults.cardElevation(0.dp)) {
        Column(Modifier.padding(16.dp), horizontalAlignment = Alignment.CenterHorizontally) {
            Icon(icon, null, tint = color, modifier = Modifier.size(32.dp))
            Spacer(Modifier.height(8.dp))
            Text(label, fontWeight = FontWeight.SemiBold, color = color)
        }
    }
}
