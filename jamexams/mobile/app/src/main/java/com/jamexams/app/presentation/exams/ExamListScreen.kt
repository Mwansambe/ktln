package com.jamexams.app.presentation.exams

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.lazy.rememberLazyListState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.*
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.jamexams.app.domain.model.Exam
import com.jamexams.app.presentation.auth.JamGreen
import com.jamexams.app.presentation.auth.JamRed

/**
 * ExamListScreen - Main exam browsing screen.
 * Handles: Loading / Error / Empty / Success states.
 * Supports: infinite scroll pagination, filters.
 */
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ExamListScreen(
    onExamClick:  (Exam) -> Unit,
    viewModel:    ExamViewModel = hiltViewModel(),
) {
    val uiState       by viewModel.uiState.collectAsStateWithLifecycle()
    val downloadState by viewModel.downloadState.collectAsStateWithLifecycle()
    val filter        by viewModel.filter.collectAsStateWithLifecycle()
    val listState      = rememberLazyListState()
    var showFilter     by remember { mutableStateOf(false) }

    // Download snackbar
    val snackbarHost = remember { SnackbarHostState() }
    LaunchedEffect(downloadState) {
        when (downloadState) {
            is DownloadState.Done  -> {
                snackbarHost.showSnackbar("Download complete!")
                viewModel.resetDownloadState()
            }
            is DownloadState.Error -> {
                snackbarHost.showSnackbar((downloadState as DownloadState.Error).message)
                viewModel.resetDownloadState()
            }
            else -> {}
        }
    }

    // Load more on scroll to bottom
    val shouldLoadMore by remember {
        derivedStateOf {
            val layoutInfo = listState.layoutInfo
            val lastIndex  = layoutInfo.visibleItemsInfo.lastOrNull()?.index ?: 0
            lastIndex >= layoutInfo.totalItemsCount - 3
        }
    }
    LaunchedEffect(shouldLoadMore) {
        if (shouldLoadMore) viewModel.loadMore()
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Column {
                        Text("Exams", fontWeight = FontWeight.Bold)
                        if (filter.subjectId != null || filter.search != null) {
                            Text("Filtered", style = MaterialTheme.typography.labelSmall, color = JamGreen)
                        }
                    }
                },
                actions = {
                    IconButton(onClick = { viewModel.loadExams(reset = true) }) {
                        Icon(Icons.Default.Refresh, "Refresh")
                    }
                    IconButton(onClick = { showFilter = !showFilter }) {
                        Icon(
                            Icons.Default.FilterList,
                            "Filter",
                            tint = if (filter != com.jamexams.app.domain.model.ExamFilter()) JamGreen else LocalContentColor.current,
                        )
                    }
                },
            )
        },
        snackbarHost = { SnackbarHost(snackbarHost) },
    ) { padding ->

        LazyColumn(
            state       = listState,
            modifier    = Modifier.fillMaxSize().padding(padding),
            contentPadding = PaddingValues(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp),
        ) {
            // State handling
            when (val state = uiState) {
                is ExamUiState.Loading -> item { LoadingState() }
                is ExamUiState.Error   -> item { ErrorState(state.message) { viewModel.loadExams() } }
                is ExamUiState.Empty   -> item { EmptyState() }
                is ExamUiState.Success -> {
                    items(state.exams, key = { it.id }) { exam ->
                        ExamCard(
                            exam      = exam,
                            onClick   = { onExamClick(exam) },
                            onDownload = { viewModel.downloadExam(exam) },
                        )
                    }
                    if (state.hasMore) {
                        item {
                            Box(Modifier.fillMaxWidth(), contentAlignment = Alignment.Center) {
                                CircularProgressIndicator(Modifier.size(24.dp), strokeWidth = 2.dp)
                            }
                        }
                    }
                }
                else -> {}
            }
        }

        // Download overlay
        if (downloadState is DownloadState.Downloading) {
            Box(Modifier.fillMaxSize().background(Color.Black.copy(alpha = 0.4f)), contentAlignment = Alignment.Center) {
                Card(shape = RoundedCornerShape(16.dp)) {
                    Column(
                        modifier = Modifier.padding(32.dp),
                        horizontalAlignment = Alignment.CenterHorizontally,
                        verticalArrangement = Arrangement.spacedBy(16.dp),
                    ) {
                        CircularProgressIndicator(color = JamGreen)
                        Text("Downloading PDF...", fontWeight = FontWeight.Medium)
                    }
                }
            }
        }
    }
}

@Composable
fun ExamCard(exam: Exam, onClick: () -> Unit, onDownload: () -> Unit) {
    val subjectColor = try {
        Color(android.graphics.Color.parseColor(exam.subject?.color ?: "#3B82F6"))
    } catch (e: Exception) { Color(0xFF3B82F6) }

    Card(
        modifier  = Modifier.fillMaxWidth().clickable(onClick = onClick),
        shape     = RoundedCornerShape(14.dp),
        elevation = CardDefaults.cardElevation(3.dp),
    ) {
        Row(modifier = Modifier.padding(16.dp), horizontalArrangement = Arrangement.spacedBy(12.dp)) {
            // Subject color indicator
            Box(
                modifier = Modifier
                    .size(48.dp)
                    .background(subjectColor.copy(alpha = 0.15f), RoundedCornerShape(10.dp)),
                contentAlignment = Alignment.Center,
            ) {
                Icon(Icons.Default.Description, null, tint = subjectColor, modifier = Modifier.size(24.dp))
            }

            Column(modifier = Modifier.weight(1f)) {
                Text(exam.title, fontWeight = FontWeight.SemiBold, maxLines = 2)
                Spacer(Modifier.height(4.dp))
                Row(horizontalArrangement = Arrangement.spacedBy(6.dp)) {
                    exam.subject?.let {
                        AssistChip(
                            onClick = {},
                            label = { Text(it.name, fontSize = 10.sp) },
                            modifier = Modifier.height(22.dp),
                        )
                    }
                    exam.year?.let {
                        AssistChip(
                            onClick = {},
                            label = { Text(it.toString(), fontSize = 10.sp) },
                            modifier = Modifier.height(22.dp),
                        )
                    }
                    if (exam.hasMarkingScheme) {
                        AssistChip(
                            onClick = {},
                            label = { Text("✓ Marking", fontSize = 10.sp) },
                            colors = AssistChipDefaults.assistChipColors(
                                containerColor = JamGreen.copy(alpha = 0.1f),
                                labelColor = JamGreen,
                            ),
                            modifier = Modifier.height(22.dp),
                        )
                    }
                }
                Text(
                    "${exam.examFileSize / 1024} KB • ${exam.examType}",
                    style = MaterialTheme.typography.bodySmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                )
            }

            IconButton(onClick = onDownload) {
                Icon(Icons.Default.Download, "Download", tint = JamGreen)
            }
        }
    }
}

@Composable
fun LoadingState() {
    Box(Modifier.fillMaxWidth().padding(40.dp), contentAlignment = Alignment.Center) {
        Column(horizontalAlignment = Alignment.CenterHorizontally, verticalArrangement = Arrangement.spacedBy(12.dp)) {
            CircularProgressIndicator(color = JamGreen)
            Text("Loading exams...", color = MaterialTheme.colorScheme.onSurfaceVariant)
        }
    }
}

@Composable
fun ErrorState(message: String, onRetry: () -> Unit) {
    Column(
        Modifier.fillMaxWidth().padding(40.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.spacedBy(16.dp),
    ) {
        Icon(Icons.Default.CloudOff, null, modifier = Modifier.size(56.dp), tint = JamRed)
        Text("Something went wrong", fontWeight = FontWeight.SemiBold, fontSize = 18.sp)
        Text(message, style = MaterialTheme.typography.bodyMedium, color = MaterialTheme.colorScheme.onSurfaceVariant)
        Button(onClick = onRetry, colors = ButtonDefaults.buttonColors(containerColor = JamGreen)) {
            Icon(Icons.Default.Refresh, null, Modifier.size(16.dp))
            Spacer(Modifier.width(8.dp))
            Text("Try Again")
        }
    }
}

@Composable
fun EmptyState() {
    Column(
        Modifier.fillMaxWidth().padding(40.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.spacedBy(12.dp),
    ) {
        Icon(Icons.Default.SearchOff, null, modifier = Modifier.size(56.dp), tint = Color.Gray)
        Text("No Exams Found", fontWeight = FontWeight.SemiBold, fontSize = 18.sp)
        Text("Try adjusting your filters or check back later.", color = MaterialTheme.colorScheme.onSurfaceVariant)
    }
}
