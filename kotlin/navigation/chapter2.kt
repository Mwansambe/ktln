fun main(){
    println("this is the main function message")
    great()
    lambda()
    onExamClick("12345")
}

fun great(){
    println("good eveining friend")
}


var lambda = {
    println("this is lambda function")
}

val onExamClick = { examId: String ->
println("exam with id $examId is clicked")
}

