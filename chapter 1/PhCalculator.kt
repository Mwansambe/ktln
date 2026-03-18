import java.time.LocalDateTime

fun main() {
    println("=============================")
    println("Soil pH Analyser - Version 1.0")
    println("=============================")
    println()
    print("Enter soil ph value (0-14): ")

    val input = readLine() // reads the line from the keyboard
    val ph = input?.toDoubleOrNull() // safely convert to Double or null

    // INPUT VALIDATION
    if (ph == null) {
        println("X Error: Please enter a valid number.")
        return
    }
    if (ph !in 0.0..14.0) {
        println("X Error: ph must be between 0 and 14. You entered: $ph")
        return
    }

    // Record and print result
    val recommendation = analysePh(ph)
    val timestamp = LocalDateTime.now().toString()
    val reading = SoilReading(ph, timestamp, recommendation)

    println()
    println("Soil pH Analysis Result:")
    println("ph value: $ph")
    println("Recorded at: $timestamp")
    println("Assessment: $recommendation")
    println()
    println("Reading saved successfully!")
}

// Data model
data class SoilReading(
    val ph: Double,
    val timestamp: String,
    val recommendation: String
)

// Function for analysis of ph
fun analysePh(ph: Double): String {
    return when {
        ph < 5.5 -> "Highly Acidic - Apply 2-3 tons/ha of agricultural lime immediately."
        ph in 5.5..6.19 -> "Slightly Acidic - Apply 1 ton/ha lime. Yield reduced ~20%."
        ph in 6.2..6.8 -> "Ideal for beans - No treatment needed. Plant now."
        ph in 6.81..7.5 -> "Neutral/Alkaline - Monitor closely, optimal is ph 6.5."
        else -> "Highly alkaline - Apply elemental sulfur 1-2 tons/ha."
    }
}
