# 🌱 Smart Garden

## 📄 Description du projet

**Smart Garden** est une application web full-stack intégrant l’IoT, conçue pour aider les utilisateurs à gérer intelligemment leurs jardins. Le projet combine des capteurs connectés (ESP32), une interface web moderne, et des fonctionnalités intelligentes pour le suivi des plantes, des capteurs et des activités de jardinage.

### Objectifs principaux

* Surveiller en temps réel la température et l’humidité
* Centraliser la gestion des plantes et des tâches
* Faciliter l’interaction entre utilisateurs via une plateforme sociale
* Introduire des fonctionnalités intelligentes (IA et automatisation)

---

## 📑 Table des matières

* [Description du projet](#-description-du-projet)
* [Installation](#-installation)
* [Utilisation](#-utilisation)
* [Architecture du projet](#-architecture-du-projet)
* [Contribution](#-contribution)
* [Licence](#-licence)

---

## ⚙️ Installation

1. **Cloner le repository**

   ```bash
   git clone https://github.com/souhailserghine/Smart-Garden.git
   cd Smart-Garden
   ```

2. **Lancer le serveur local**

   * Installer **XAMPP** (Apache + MySQL)
   * Démarrer Apache et MySQL

3. **Créer la base de données**

   * Ouvrir `http://localhost/phpmyadmin`
   * Créer une base de données nommée `smartgarden`
   * Importer le fichier SQL du projet ou exécuter les scripts fournis

4. **Configurer la connexion à la base de données**

   * Modifier le fichier :

     ```php
     app/config.php
     ```

5. **Installer les dépendances PHP**

   ```bash
   cd app
   composer install
   ```

---

## ▶️ Utilisation

### 🌐 Accès à l’application

* **Front Office (utilisateur)** :

  ```
  http://localhost/website/Smart-Garden/app/view/frontoffice/plantes.php
  ```

* **Back Office (administrateur)** :

  ```
  http://localhost/website/Smart-Garden/app/view/backoffice/backoffice.php
  ```

### 🔌 Utilisation des capteurs IoT

* Les capteurs ESP32 envoient automatiquement les données vers le serveur
* Les données sont affichées en temps réel sur le tableau de bord
* L’historique est consultable via des graphiques

### 🤖 Fonctionnalités intelligentes

* Assistant IA pour conseils de jardinage
* Recommandations automatiques basées sur les données des capteurs

---

## 🏗️ Architecture du projet

* **Backend** : PHP (architecture MVC), MySQL
* **Frontend** : HTML, CSS, JavaScript, Bootstrap
* **IoT** : ESP32 + capteurs DHT
* **API** : REST (JSON)
* **IA** : Chatbot et recommandations automatisées

Structure simplifiée :

```
Smart-Garden/
├── app/
│   ├── controller/
│   ├── model/
│   ├── view/
│   ├── api/
│   └── esp32/
├── vendor/
├── README.md
└── composer.json
```

---

## 🤝 Contribution

Les contributions sont les bienvenues.

1. Forker le projet
2. Créer une branche :

   ```bash
   git checkout -b feature/nouvelle-fonctionnalite
   ```
3. Commit vos modifications :

   ```bash
   git commit -m "Ajout d’une nouvelle fonctionnalité"
   ```
4. Push la branche :

   ```bash
   git push origin feature/nouvelle-fonctionnalite
   ```
5. Ouvrir une Pull Request

---

## 📜 Licence

Ce projet est sous licence **ESPRIT**.

Vous êtes libre de :

* Utiliser le projet
* Modifier le code
* Redistribuer le projet

Sous réserve de conserver la mention de copyright et la licence.

---

## 👨‍💻 Auteur

* **Souhail Serghine**

