CREATE DATABASE absence_management;

USE absence_management;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('surveillant', 'directeur') NOT NULL
);

CREATE TABLE stagiaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    filiere VARCHAR(100) NOT NULL,
    groupe VARCHAR(100) NOT NULL,
    academic_year varchar(255) not null,
    cin varchar(255),
    email VARCHAR(255)
);

CREATE TABLE absences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stagiaire_id int,
    filiere varchar(255) ,
    group_number INT,
    absence_date DATE NOT NULL,
    FOREIGN KEY (stagiaire_id) references stagiaires(id),
    status ENUM('absence justifiée', 'absence injustifiée', 'présence') NOT NULL,
    hours INT NOT NULL
);


INSERT INTO stagiaires (name, filiere, groupe, academic_year,cin,email) VALUES
('Adam Martin', 'Developpement', '201', '2024/2025','TJ8899',"email@gmail.com"),
('Hamid Dupont', 'Security', '103', '2023/2024','TJ8899',"email@gmail.com"),
('Kamal Durand', 'TM', '300', '2022/2023','TJ8899',"email@gmail.com"),
('Hanane Petit', 'Developpement', '201', '2022/2023','TJ8899',"email@gmail.com"),
('Haya Moreau', 'Security', '103', '2024/2025','TJ8899',"email@gmail.com"),
('Abd elhak Leblanc', 'TM', '300', '2022/2023','TJ8899'),
('Grace Robert', 'Developpement', '201', '2022/2023','TJ8899',"email@gmail.com"),
('Hugo Vincent', 'Security', '103', '2023/2024','TJ8899',"email@gmail.com"),
('Ivy Garnier', 'TM', '300', '2022/2023','TJ8899',"email@gmail.com"),
('Jack Lambert', 'Developpement', '201', '2024/2025','TJ8899',"email@gmail.com");