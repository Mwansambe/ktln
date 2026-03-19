fun main(){
    val data: String? = "9.90"
    //!! tell the compiler that data is not null, if it is null throw an exception
    val length = data!!.length //works if data is not null, but throws NullPointerException if data is null
    //Dangerous
    val noData: String? = null

    if (noData != null){
        val safe = noData.length //smart cast kotlin shows tht it is not null
    }

}