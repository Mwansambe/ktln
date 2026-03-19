fun getSoilReport(location: String?): String {
    return when (location) {
        null -> "Location not provided"
        "Farm A" -> "Soil is fertile"
        "Farm B" -> "Soil is sandy"
        else -> "Unknown location"
    }
}

fun main() {
    println(getSoilReport("Farm A"))   // Soil is fertile
    println(getSoilReport("Farm B"))   // Soil is sandy
    println(getSoilReport(null))       // Location not provided
    println(getSoilReport("Farm C"))   // Unknown location
}
