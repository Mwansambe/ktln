data class SoilAnalysis(
    val condition: String,
    val recommendation: String,
    val limeRequired: Double //tons per hectare
)

fun fullSoilAnalysis(ph: Double): SoilAnalysis {
    return when {
        ph < 5.0 -> SoilAnalysis(
            condition = "Severely Acidic",
            recommendation = "Apply agricutural lime immedietly do not plant beans",
            limeRequired = 3.0
        )
        ph < 5.5 -> SoilAnalysis(
            condition = "Higly Acidic",
            recommendation = "Apply lime manure",
            limeRequired = 2.0
        )
                ph in 5.5..6.19 -> SoilAnalysis(
            condition      = "Slightly Acidic",
            recommendation = "Light lime application. Beans will grow but yield is reduced.",
            limeRequired   = 1.0
        )
      ph in 6.2..6.8 -> SoilAnalysis(
                    condition      = "Ideal for Beans ✅",
            recommendation = "No treatment needed. Perfect planting conditions.",
            limeRequired   = 0.0
        )
        ph in 6.81..7.5 -> SoilAnalysis(
            condition      = "Neutral/Slightly Alkaline",
            recommendation = "Monitor closely. Yield may be suboptimal.",
            limeRequired   = 0.0
        )
        else -> SoilAnalysis(
            condition      = "Highly Alkaline",
            recommendation = "Apply elemental sulfur (1–2 tons/ha).",
            limeRequired   = 0.0
        )

    }
}

fun main() {
    val ph = 4.8
    val analysis = fullSoilAnalysis(ph)
    println("pH $ph — ${analysis.condition}")
    println("Recommendation: ${analysis.recommendation}")
    if (analysis.limeRequired > 0) {
        println("Apply ${analysis.limeRequired} tons/ha of lime")
    }
}
