package com.jamexams.app.presentation.exams

import androidx.compose.animation.*
import androidx.compose.foundation.*
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.*
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.hilt.navigation.compose.hiltViewModel
import com.jamexams.app.domain.model.Exam
import com.jamexams.app.domain.model.Subject
import com.jamexams.app.ui.components.ErrorView
import com.jamexams.app.ui.components.EmptyView
import com.jamexams.app.ui.components.LoadingView

/**
 * ExamsScreen - Displays a filterable, searchable list of exam papers.
 */
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ExamsScreen(
    onExamClick: (Int) -> Unit,
    viewModel: ExamsViewModel = hiltViewModel()
) {
    val uiState     by viewModel.uiState.collectAsState()
    val downloadState by viewModel.downloadState.collectAsState()

    var showFilterSheet by remember { mutableStateOf(false) }
    var searchQuery     by remember { mutableStateOf("") }

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Column {
                        Text("Exam Papers", fontWeight = FontWeight.Bold, fontSize = 20.sp)
                        Text(
                            "${uiState.exams.size} papers available",
                            fontSize = 12.sp,
                            color = MaterialTheme.colorScheme.onSurface.copy(alpha = 0.6f)
                        )
                    }
                },
                actions = {
                    IconButton(onClick = { showFilterSheet = true }) {
                        BadgedBox(badge = {
                            val activeFilters = listOfNotNull(
                                uiState.filter.subjectId,
                                uiState.filter.classLevel,
                                uiState.filter.examType,
                                uiState.filter.year
                            ).size
                            if (activeFilters > 0) Badge { Text("$activeFilters") }
                        }) {
                            Icon(Icons.Default.FilterList, "Filter")
                        }
                    }
                }
            )
        }
    ) { padding ->
        Column(modifier = Modifier.padding(padding)) {

            // Search Bar
            OutlinedTextField(
                value = searchQuery,
                onValueChange = {
                    searchQuery = it
                    viewModel.applyFilter(uiState.filter.copy(search = it.ifBlank { null }))
                },
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp, vertical = 8.dp),
                placeholder = { Text("Search exams...") },
                leadingIcon = { Icon(Icons.Default.Search, null) },
                trailingIcon = {
                    if (searchQuery.isNotEmpty()) {
                        IconButton(onClick = {
                            searchQuery = ""
                            viewModel.applyFilter(uiState.filter.copy(search = null))
                        }) { Icon(Icons.Default.Clear, null) }
                    }
                },
                singleLine = true,
                shape = RoundedCornerShape(12.dp)
            )

            // Subject chips
            if (uiState.subjects.isNotEmpty()) {
                SubjectFilterChips(
                    subjects      = uiState.subjects,
                    selectedId    = uiState.filter.subjectId,
                    onSelect      = { id -> viewModel.applyFilter(uiState.filter.copy(subjectId = id)) }
                )
            }

            // Content area
            when {
                uiState.isLoading -> LoadingView()
                uiState.errorMessage != null -> ErrorView(
                    message = uiState.errorMessage!!,
                    onRetry = { viewModel.loadExams() }
                )
                uiState.isEmpty -> EmptyView(
                    message = "No exams found",
                    onClearFilter = { viewModel.clearFilter() }
                )
                else -> {
                    LazyColumn(
                        contentPadding = PaddingValues(16.dp),
                        verticalArrangement = Arrangement.spacedBy(12.dp)
                    ) {
                        items(uiState.exams, key = { it.id }) { exam ->
                            ExamCard(
                                exam      = exam,
                                onClick   = { onExamClick(exam.id) },
                                isDownloading = downloadState.isDownloading && downloadState.examId == exam.id,
                                downloadProgress = if (downloadState.examId == exam.id) downloadState.progress else 0
                            )
                        }
                    }
                }
            }
        }
    }
}

@Composable
fun SubjectFilterChips(
    subjects: List<Subject>,
    selectedId: Int?,
    onSelect: (Int?) -> Unit
) {
    LazyRow(
        contentPadding = PaddingValues(horizontal = 16.dp),
        horizontalArrangement = Arrangement.spacedBy(8.dp),
        modifier = Modifier.padding(vertical = 4.dp)
    ) {
        item {
            FilterChip(
                selected  = selectedId == null,
                onClick   = { onSelect(null) },
                label     = { Text("All") },
                leadingIcon = if (selectedId == null) ({
                    Icon(Icons.Default.Done, null, Modifier.size(16.dp))
                }) else null
            )
        }
        items(subjects, key = { it.id }) { subject ->
            FilterChip(
                selected  = selectedId == subject.id,
                onClick   = { onSelect(if (selectedId == subject.id) null else subject.id) },
                label     = { Text(subject.name) },
                leadingIcon = if (selectedId == subject.id) ({
                    Icon(Icons.Default.Done, null, Modifier.size(16.dp))
                }) else null
            )
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ExamCard(
    exam: Exam,
    onClick: () -> Unit,
    isDownloading: Boolean = false,
    downloadProgress: Int  = 0
) {
    val subjectColor = exam.subject?.color?.let {
        try { Color(android.graphics.Color.parseColor(it)) } catch (e: Exception) { Color(0xFF1B5E20) }
    } ?: Color(0xFF1B5E20)

    Card(
        onClick  = onClick,
        modifier = Modifier.fillMaxWidth(),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
        shape = RoundedCornerShape(12.dp)
    ) {
        Column(modifier = Modifier.padding(16.dp)) {
            Row(verticalAlignment = Alignment.CenterVertically) {
                // Subject color dot
                Box(
                    modifier = Modifier
                        .size(44.dp)
                        .clip(CircleShape)
                        .background(subjectColor.copy(alpha = 0.15f)),
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        Icons.Default.MenuBook,
                        contentDescription = null,
                        tint = subjectColor,
                        modifier = Modifier.size(22.dp)
                    )
                }

                Spacer(modifier = Modifier.width(12.dp))

                Column(modifier = Modifier.weight(1f)) {
                    Text(
                        text = exam.title,
                        fontWeight = FontWeight.SemiBold,
                        fontSize = 15.sp,
                        maxLines = 2,
                        overflow = TextOverflow.Ellipsis
                    )
                    Text(
                        text = buildString {
                            exam.subject?.name?.let { append(it) }
                            exam.classLevel?.let { append(" · $it") }
                            exam.year?.let { append(" · $it") }
                        },
                        fontSize = 12.sp,
                        color = MaterialTheme.colorScheme.onSurface.copy(alpha = 0.6f)
                    )
                }

                if (exam.isFeatured) {
                    Icon(Icons.Default.Star, null, tint = Color(0xFFFFC107), modifier = Modifier.size(20.dp))
                }
            }

            Spacer(modifier = Modifier.height(8.dp))

            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                AssistChip(
                    onClick = {},
                    label   = { Text(exam.examType.replace("_", " "), fontSize = 11.sp) }
                )
                AssistChip(
                    onClick = {},
                    label   = { Text(exam.examFileSizeFormatted, fontSize = 11.sp) }
                )
                if (exam.hasMarkingScheme) {
                    AssistChip(
                        onClick = {},
                        label   = { Text("+ Marking", fontSize = 11.sp) },
                        colors  = AssistChipDefaults.assistChipColors(
                            containerColor = Color(0xFFE8F5E9)
                        )
                    )
                }
            }

            if (isDownloading) {
                Spacer(modifier = Modifier.height(8.dp))
                LinearProgressIndicator(
                    progress = { downloadProgress / 100f },
                    modifier = Modifier.fillMaxWidth()
                )
                Text("Downloading... $downloadProgress%", fontSize = 11.sp, color = Color.Gray)
            }
        }
    }
}
