import java.time.LocalDateTime

class SoilSample(
    val ph: Double,
    val location: String,
    val farmerName: String,
    val cropType: String = "beans",
    val timestamp: String = LocalDateTime.now().toString()
) {
    init {
        require(ph in 0.0..14.0) { "ph must be 0 - 14" }
        require(location.isNotBlank()) { "location cannot be blank" }
        require(farmerName.isNotBlank()) { "farmer name required" }
    }

    // Secondary constructor from CSV line
    constructor(csvLine: String) : this(
        ph = csvLine.split(",")[0].toDouble(),
        location = csvLine.split(",")[1].trim(),
        farmerName = csvLine.split(",")[2].trim(),
        cropType = csvLine.split(",").getOrElse(3) { "beans" }.trim()
    )

    fun classify(): String = when {
        ph < 5.5 -> "Highly acidic. Apply lime urgently!"
        ph <= 6.8 -> "Ideal for $cropType"
        else -> "Alkaline. Consider adding sulfur to lower pH."
    }

    fun needsLime() = ph < 5.5

    fun limeRequired(): Double = when {
        ph < 5.0 -> 3.0
        ph < 5.5 -> 2.0
        ph < 6.2 -> 1.0
        else -> 0.0
    }

    override fun toString(): String =
        "SoilSample(ph=$ph, location=$location, farmerName=$farmerName, cropType=$cropType, timestamp=$timestamp)"
}

fun main() {
    val s1 = SoilSample(4.8, "Field A", "Mwana", "Beans")
    println(s1)
    println("Status: ${s1.classify()}")
    println("Lime needed: ${s1.limeRequired()} tons/ha")

    // From CSV
    val s2 = SoilSample("6.5, Field B, Juma, Maize")
    println(s2)
    println("Status: ${s2.classify()}")
    println("Lime needed: ${s2.limeRequired()} tons/ha")
}
