import requests

domain_name = "https://moodle.unilu.ac.cd/"
token = "token_delivre_par_moodle"

server_url = domain_name + "webservice/rest/server.php"
print(server_url)

params = {
    "wstoken": token,
    "moodlewsrestformat":"json",
    "wsfunction": "enrol_unilu_sync_course",
    "fullname": "BIO102-BiologieTest-202324", # nom complet reprenant l'abbréviation-titre-annee académique
    "shortname": "BIO102-0-202324", # nom abrege : abbreviation-0-annee academique
    "idnumber": "BIO102-0-202324", # identifiant du cours : abbreviation-0-annee academique
    "categoryidnumber": "BAC1 SBM 0101" # identificant de la promotion (avec espace ou tiret "-")
}


response = requests.post(server_url, params = params)
# Check the response status code to ensure the request was successful
if response.status_code == 200:
    # Print the response data
    print(response.json())
else:
    # Print an error message
    print("An error occurred while sending the request.")