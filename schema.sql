CREATE DATABASE autovermietung;

USE autovermietung;

CREATE TABLE fahrzeuge (
  id INT AUTO_INCREMENT PRIMARY KEY,
  marke VARCHAR(10),
  modell VARCHAR(10),
  jahrgang INT,
  kategorie INT,
  verfuegbar BOOLEAN
);

CREATE TABLE buchungen (
  id INT AUTO_INCREMENT PRIMARY KEY,
  benutzer_name VARCHAR(50),
  fahrzeug_id INT,
  datum DATETIME,
  dauer INT,
  benutzer_id INT,
  FOREIGN KEY (fahrzeug_id) REFERENCES fahrzeuge(id)
);


INSERT INTO fahrzeuge (marke, modell, jahrgang, kategorie, verfuegbar) VALUES
('Toyota', 'Corolla', 2018, 1, TRUE),
('BMW', 'X5', 2020, 2, FALSE),
('Audi', 'A4', 2019, 3, TRUE),
('Ford', 'Focus', 2015, 1, TRUE),
('Honda', 'Civic', 2017, 1, TRUE),
('Mercedes', 'GLC', 2021, 2, FALSE),
('Volkswagen', 'Golf', 2016, 1, TRUE),
('Hyundai', 'Elantra', 2018, 1, TRUE),
('Nissan', 'Altima', 2019, 3, FALSE),
('Chevrolet', 'Malibu', 2020, 3, TRUE),
('Kia', 'Sportage', 2021, 2, TRUE),
('Mazda', 'CX-5', 2022, 2, TRUE),
('Subaru', 'Outback', 2018, 3, FALSE),
('Tesla', 'Model 3', 2021, 4, TRUE),
('Volvo', 'XC90', 2020, 2, TRUE),
('Jeep', 'Wrangler', 2017, 2, FALSE),
('Lexus', 'RX', 2019, 2, TRUE),
('Porsche', 'Cayenne', 2021, 4, TRUE),
('Fiat', '500', 2015, 1, TRUE),
('Peugeot', '208', 2016, 1, FALSE);
