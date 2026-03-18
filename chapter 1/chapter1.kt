fun  main(){
    println(greatFamer("MWAPAGHATA"))
println(isIdeal(5.3))
println(isIdeal(6.5))


println(analyseForCrop(3.4))

println(analyseForCrop(5.0, "yams"))
println(cropName(2))
}
fun greatFamer(name: String):String{
    return "Hello $name welcome to soil decision support app"
}
fun isIdeal(ph: Double) = ph in 6.5..6.8

fun describePh(ph: Double): String {
    return when {
        ph <5.5 -> "it is very acidic"
        ph in 5.5..6.19 -> "Ideal for beans"
        ph in 6.81..7.5 -> "Neutral / alkaline"
        else -> "Higly alkaline"
    }
}

fun analyseForCrop(ph: Double, crop: String = "beans"): String{
    return "For crop $crop: ${describePh(ph)}"
}

fun cropName(code: Int): String = when (code){
    1 -> "Beans"
    2 -> "Maize"
    3 -> "Coffee"
    else -> "Unknown crop"

}