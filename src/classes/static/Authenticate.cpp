#DEFINE SERVER_SECRET "LUXisAwesome!"
using namespace std;

string Authenticate::createAccessToken(const string EUID, const string timeBucket){
    // NEEDS TO BE DONE
    //MD5
	
	
	return "afalsdasda";
}

string Authenticate::authenticateJWT(const string JWT, const string Client_API_KEY){
	string res = "";
    BSONObj bjwt = mongo::fromjson(JWT);
	if(bjwt != NULL) {
		res = bjwt.hasField("jti")? bjwt["jti"].toString() : bjwt["JTI"].toString();            //JWT ID Claim
	}
	return  res; // return unique ID
}

string Authenticate::createNewEUID(const string uniqueID)
{
	//NEEDS TO BE DONE
	//MD5
	return "new ID";

}



string Authenticate::createAccessToken(string EUID){
    return Authenticate::createAccessToken(EUID, Authenticate::getTimeBucket());
}

bool Authenticate::authenticateAccessToken(string AccessToken, string EUID){
    string newAccessToken = Authenticate::createAccessToken(EUID);
    if(AccessToken.compare(newAccessToken)){
        return true;
    }else{
        return false;
    }
}

string Authenticate::refreshAccessToken(string AccessToken, string EUID){
    string newHash = Authenticate::createAccessToken(EUID, Authenticate::getTimeBucket());
    if(AccessToken.compare(newHash)){
        return AccessToken;
    }else if(AccessToken.compare(Authenticate::createAccessToken(EUID, Authenticate::getPreviousTimeBucket()))){
        return newHash;
    }else{
        return "";
    }
}

string Authenticate::getTimeBucket(){
        time_t timer;
        struct tm y2k;
        double seconds;

        y2k.tm_hour = 0;   y2k.tm_min = 0; y2k.tm_sec = 0;
        y2k.tm_year = 100; y2k.tm_mon = 0; y2k.tm_mday = 1;

        time(&timer);  /* get current time; same as: timer = time(NULL)  */

        seconds = difftime(timer,mktime(&y2k));

        int timeBucket = (int)seconds/14336;

    return to_string(timeBucket);
}

string Authenticate::getPreviousTimeBucket(){
    return to_string(atoi(Authenticate::getTimeBucket())-1);
}



