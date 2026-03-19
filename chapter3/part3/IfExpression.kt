fun main() {
    val ph = 6.0

    // ── STATEMENT style ───────────────────────────────
    // Declare a mutable variable and assign inside if/else
    var status: String
    if (ph < 5.5) {
        status = "Acidic"
    } else {
        status = "Good"
    }
    println("Soil pH status: $status")

    // ── EXPRESSION style ──────────────────────────────
    // Assign directly using if as an expression
    status = if (ph < 5.5) "Acidic" else "Good"
    println("Soil pH status: $status")

    // ── MULTI-LINE if expression ─────────────────────
    // More complex logic inside branches, still returns a value
    val recommendation = if (ph < 5.5) {
        val limeAmount = calculateLime(ph)
        "Apply $limeAmount tons/ha lime"
    } else {
        "No treatment needed"
    }
    println("Recommendation: $recommendation")
}

// ── Function to calculate lime requirement ──────────
// Takes soil pH as input and returns lime amount
fun calculateLime(ph: Double): Double {
    // Simple formula: difference from 5.5 multiplied by 2
    return (5.5 - ph) * 2.0
}
