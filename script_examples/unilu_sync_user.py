import requests

domain_name = "https://moodle.unilu.ac.cd/"
token = "token_delivre_par_moodle"

server_url = domain_name + "webservice/rest/server.php"
print(server_url)

params = {
    "wstoken": token,
    "moodlewsrestformat":"json",
    "wsfunction": "enrol_unilu_sync_user",
    "firstname": "Chris",
    "lastname": "Mertens",
    "username": "chris.mertens", # nom d'utilisateur
    "email": "mon_mail@unilu.ac.cd", # mail
    "phone1": "0123456", # telephone
    "aa": "202324",  # annee academique
    "matricule": "0000123", # code général
    "faculte": "FMED", # code de la faculte
    "promotion": "BAC1 SBM 0101 ", # identificant de la promotion (avec espace ou tiret "-")
    "statut": "student", # statut officiel à l'UNILU
    "suspended": 0 # le compte doit-il être suspendu ? si oui, => 1
}


response = requests.post(server_url, params = params)
# Check the response status code to ensure the request was successful
if response.status_code == 200:
    # Print the response data
    print(response.json())
else:
    # Print an error message
    print("An error occurred while sending the request.")