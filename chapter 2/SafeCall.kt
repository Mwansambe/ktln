fun main(){
    val rawData: String? = getSensorReading()
     //unsafe if raw data is null
     //val length = rawData.length
     val length = rawData?.length
     println("Data length: $length")

     //chain mutiple safe calls
     val firstChar =rawData?.trim()?.firstOrNull()
     println("First character: $firstChar")
}
fun getSensorReading(): String? {
    // Simulate a sensor reading that might be null
    return if (Math.random() > 0.5) "  Hello  " else null
}