// pattern with match values
fun main(){
    var dayNumber = 0
val dayName = when (dayNumber){
    1 -> "Monday"
    2 -> "Tuesday"
    6, 7 -> "weekend"
    else -> "weekday"
}
println(dayName)
println(classfySoil(2.3))
println(recommend(2.3))
println(analyseNullable(null))

}
//pattern 2: match ranges and most usefulf for ph
fun classfySoil(ph: Double): String {
    return  when (ph) {
        in 0.0..5.4 -> "Highlu acidic"
        in 5.5..6.19 -> "Slightly Acidic"
        in 6.2..6.8 -> "Ideal for Beans"
        in 6.81..7.5 -> "Neutral/Alkaline"
        in 7.51..14.0 -> "Highly Alkaline"
        else  -> "Invalid pH value"
    }
}

//pattern 3: Boolean conditions (no arguments)
fun recommend(ph: Double): String {
    return when {
        ph < 5.5 -> "Apply lime urgently - 2 tons/ha"
        ph <= 6.8 -> "pH is ideal - no treatment needed"
        ph <= 8.0 -> "Sligtly alkaline - monitor soil"
        else -> "Highly alkaline  - apply elemental sulfur"
    }
}

// pattern 4: Handle null in when
fun analyseNullable(ph: Double?): String {
    return when (ph){
        null -> "No sensor data available"
        in 6.2..6.8 -> "Ideal"
        else -> "Needs attention"
    }
}