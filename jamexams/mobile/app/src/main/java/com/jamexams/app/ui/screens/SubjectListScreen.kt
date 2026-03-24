package com.jamexams.app.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.shape.RoundedCornerShape
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
import com.jamexams.app.presentation.subjects.SubjectListViewModel
import com.jamexams.app.presentation.subjects.SubjectListUiState
import com.jamexams.app.ui.theme.JamGreen

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun SubjectListScreen(
    onSubjectClick: (Int) -> Unit,
    viewModel: SubjectListViewModel = hiltViewModel(),
) {
    val uiState by viewModel.uiState.collectAsState()

    Scaffold(
        topBar = {
            TopAppBar(title = { Text("Subjects", fontWeight = FontWeight.Bold) },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = Color.White))
        }
    ) { padding ->
        when (val state = uiState) {
            is SubjectListUiState.Loading -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator(color = JamGreen)
            }
            is SubjectListUiState.Error -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Text(state.message, color = Color.Gray)
                    Button(onClick = viewModel::load) { Text("Retry") }
                }
            }
            is SubjectListUiState.Success -> {
                LazyVerticalGrid(
                    columns = GridCells.Fixed(2),
                    modifier = Modifier.fillMaxSize().padding(padding).padding(12.dp),
                    horizontalArrangement = Arrangement.spacedBy(12.dp),
                    verticalArrangement = Arrangement.spacedBy(12.dp),
                ) {
                    items(state.subjects, key = { it.id }) { subject ->
                        val color = try { Color(android.graphics.Color.parseColor(subject.color ?: "#1B8B4B")) } catch (e: Exception) { JamGreen }
                        Card(onClick = { onSubjectClick(subject.id) }, shape = RoundedCornerShape(14.dp),
                            colors = CardDefaults.cardColors(containerColor = color.copy(alpha = 0.08f)),
                            elevation = CardDefaults.cardElevation(0.dp)) {
                            Column(Modifier.padding(16.dp), horizontalAlignment = Alignment.CenterHorizontally) {
                                Icon(Icons.Filled.MenuBook, null, tint = color, modifier = Modifier.size(36.dp))
                                Spacer(Modifier.height(8.dp))
                                Text(subject.name, fontWeight = FontWeight.SemiBold, color = color)
                                Text("${subject.examCount} papers", style = MaterialTheme.typography.labelSmall, color = Color.Gray)
                            }
                        }
                    }
                }
            }
        }
    }
}
