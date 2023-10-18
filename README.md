# Plugin d'inscription de l'UNILU

<u>**Objectifs**</u>

Mise en place du Webservice Moodle pour la synchronisation des cours, des utilisateurs et des inscriptions aux cours.

Création de 3 fonctions au niveau du web service :

- sync_course : création ou mise à jour d’un espace de cours Moodle sur la base des données issues de la maquette LMD.
- sync_user :  création ou mise à jour des comptes utilisateurs
- sync_enrolment : inscription des participants au cours (avec rôle spécifique)

<u>**Etapes de mise en place :**</u>
- **Création d'une arborescence de catégories au sein de Moodle :**

S'agissant d'une arborescence très peu variable dans le temps, la gestion des catégories reste pour le moment un processus manuel.
La maquette LMD a été utilisée. Les catégories principales sont les facultés et les sous-catégories sont les promotions.
Chaque promotion dispose d'un identifiant spécifique de la forme "BAC1-SBM-0101" 
(remarque : "BAC1 SBM 0101", avec espace, est la nomenclature utilisée au sein de GP7. En conséquence, le plugin remplace systématique les espaces par des "-").
Cet identifiant permettra de catégoriser les cours au moment de leur création.
La notion de département n'est pas considérée ici car relève des aspects administratifs.

- **Installation du plugin "unilu" sur le serveur Moodle**

Le plugin "unilu" est à installer au sein de [moodle_dir]/enrol/unilu. 

- **Paramétrage pour l'utilisation du webservice**

1. Création du user attaché au web service
2. Creation d’un rôle «webservice»  ayant des permissions spécifiques >> https://moodle.unilu.ac.cd/admin/roles/define.php?action=view&roleid=9
3. Attribuer le rôle webservice à l’utilisateur webservice >> https://moodle.unilu.ac.cd/admin/roles/assign.php?contextid=1
