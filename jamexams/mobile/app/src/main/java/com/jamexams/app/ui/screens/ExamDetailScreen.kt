package com.jamexams.app.ui.screens

import androidx.compose.foundation.layout.*
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
import com.jamexams.app.presentation.exams.ExamDetailViewModel
import com.jamexams.app.presentation.exams.ExamDetailUiState
import com.jamexams.app.ui.theme.JamGreen
import com.jamexams.app.ui.theme.JamRed

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ExamDetailScreen(
    examId: Int,
    onBack: () -> Unit,
    viewModel: ExamDetailViewModel = hiltViewModel(),
) {
    val uiState by viewModel.uiState.collectAsState()
    val downloadState by viewModel.downloadState.collectAsState()

    LaunchedEffect(examId) { viewModel.loadExam(examId) }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Exam Details", fontWeight = FontWeight.Bold) },
                navigationIcon = { IconButton(onClick = onBack) { Icon(Icons.Filled.ArrowBack, "Back") } },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = Color.White),
            )
        }
    ) { padding ->
        when (val state = uiState) {
            is ExamDetailUiState.Loading -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator(color = JamGreen)
            }
            is ExamDetailUiState.Error -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Icon(Icons.Filled.Error, null, tint = JamRed, modifier = Modifier.size(56.dp))
                    Spacer(Modifier.height(12.dp))
                    Text(state.message, color = Color.Gray)
                    Spacer(Modifier.height(12.dp))
                    Button(onClick = { viewModel.loadExam(examId) }) { Text("Retry") }
                }
            }
            is ExamDetailUiState.Success -> {
                val exam = state.exam
                val subjectColor = try { Color(android.graphics.Color.parseColor(exam.subject?.color ?: "#1B8B4B")) } catch (e: Exception) { JamGreen }

                Column(
                    Modifier.fillMaxSize().padding(padding).verticalScroll(rememberScrollState()).padding(20.dp)
                ) {
                    // Header card
                    Card(
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(16.dp),
                        colors = CardDefaults.cardColors(containerColor = subjectColor.copy(alpha = 0.08f)),
                    ) {
                        Column(Modifier.padding(20.dp)) {
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Card(
                                    Modifier.size(52.dp), shape = RoundedCornerShape(14.dp),
                                    colors = CardDefaults.cardColors(containerColor = subjectColor.copy(alpha = 0.2f)),
                                ) {
                                    Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                                        Icon(Icons.Filled.Description, null, tint = subjectColor, modifier = Modifier.size(28.dp))
                                    }
                                }
                                Spacer(Modifier.width(14.dp))
                                Column {
                                    Text(exam.subject?.name ?: "Unknown", style = MaterialTheme.typography.labelMedium, color = subjectColor, fontWeight = FontWeight.SemiBold)
                                    Text(exam.title, style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold)
                                }
                            }
                            Spacer(Modifier.height(16.dp))
                            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                                SuggestionChip(onClick = {}, label = { Text(exam.examType) })
                                exam.classLevel?.let { SuggestionChip(onClick = {}, label = { Text(it) }) }
                                exam.year?.let { SuggestionChip(onClick = {}, label = { Text(it.toString()) }) }
                            }
                            exam.description?.let {
                                Spacer(Modifier.height(12.dp))
                                Text(it, style = MaterialTheme.typography.bodyMedium, color = Color.Gray)
                            }
                        }
                    }

                    Spacer(Modifier.height(20.dp))

                    // File info
                    Card(Modifier.fillMaxWidth(), shape = RoundedCornerShape(14.dp), colors = CardDefaults.cardColors(containerColor = Color.White), elevation = CardDefaults.cardElevation(2.dp)) {
                        Column(Modifier.padding(16.dp)) {
                            Text("Files", fontWeight = FontWeight.SemiBold)
                            Spacer(Modifier.height(12.dp))
                            Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
                                Row(verticalAlignment = Alignment.CenterVertically) {
                                    Icon(Icons.Filled.PictureAsPdf, null, tint = JamRed)
                                    Spacer(Modifier.width(8.dp))
                                    Column {
                                        Text("Exam Paper", fontWeight = FontWeight.Medium)
                                        Text("${exam.examFileSize / 1024} KB", style = MaterialTheme.typography.bodySmall, color = Color.Gray)
                                    }
                                }
                                Button(
                                    onClick = { viewModel.downloadExam() },
                                    colors = ButtonDefaults.buttonColors(containerColor = JamGreen),
                                    enabled = downloadState !is ExamDetailViewModel.DownloadState.Downloading,
                                ) {
                                    if (downloadState is ExamDetailViewModel.DownloadState.Downloading) {
                                        CircularProgressIndicator(Modifier.size(16.dp), color = Color.White, strokeWidth = 2.dp)
                                    } else {
                                        Icon(Icons.Filled.Download, null, modifier = Modifier.size(18.dp))
                                        Spacer(Modifier.width(4.dp))
                                        Text("Download")
                                    }
                                }
                            }

                            if (exam.hasMarkingScheme) {
                                Divider(Modifier.padding(vertical = 12.dp))
                                Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
                                    Row(verticalAlignment = Alignment.CenterVertically) {
                                        Icon(Icons.Filled.CheckCircle, null, tint = JamGreen)
                                        Spacer(Modifier.width(8.dp))
                                        Column {
                                            Text("Marking Scheme", fontWeight = FontWeight.Medium)
                                            Text("${exam.markingSchemeSize / 1024} KB", style = MaterialTheme.typography.bodySmall, color = Color.Gray)
                                        }
                                    }
                                    OutlinedButton(onClick = { viewModel.downloadMarkingScheme() }) {
                                        Icon(Icons.Filled.Download, null, modifier = Modifier.size(18.dp))
                                        Spacer(Modifier.width(4.dp))
                                        Text("Download")
                                    }
                                }
                            }
                        }
                    }

                    Spacer(Modifier.height(12.dp))

                    // Stats
                    Card(Modifier.fillMaxWidth(), shape = RoundedCornerShape(14.dp), colors = CardDefaults.cardColors(containerColor = Color.White), elevation = CardDefaults.cardElevation(2.dp)) {
                        Row(Modifier.fillMaxWidth().padding(16.dp), horizontalArrangement = Arrangement.SpaceAround) {
                            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                Text("${exam.downloadCount}", fontWeight = FontWeight.Bold, style = MaterialTheme.typography.titleLarge, color = JamGreen)
                                Text("Downloads", style = MaterialTheme.typography.labelSmall, color = Color.Gray)
                            }
                            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                Text(if (exam.hasMarkingScheme) "Yes" else "No", fontWeight = FontWeight.Bold, style = MaterialTheme.typography.titleLarge, color = if (exam.hasMarkingScheme) JamGreen else Color.Gray)
                                Text("Marking Scheme", style = MaterialTheme.typography.labelSmall, color = Color.Gray)
                            }
                            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                Text(exam.code, fontWeight = FontWeight.Bold, style = MaterialTheme.typography.titleLarge, color = JamGreen)
                                Text("Code", style = MaterialTheme.typography.labelSmall, color = Color.Gray)
                            }
                        }
                    }
                }
            }
        }
    }
}
