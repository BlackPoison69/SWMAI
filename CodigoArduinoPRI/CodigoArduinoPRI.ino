/* PROJETO SWMAI 
Gustavo Veronezi de Carvalho
Julio Cesar Teiche Fraioli
*/

// --- Bibliotecas ---
#include <DS1302.h>
#include "DHT.h"

// --- Valores de calibração ---
const int Valor_Minimo_Chuva = 750;
const int Valor_Maximo_Chuva = 0;
const int Valor_Minimo_Umidade_Solo = 750;
const int Valor_Maximo_Umidade_Solo = 0;

// --- Portas ---
const int buzzer = 12;
const int Porta_Informacao_Analogica_Chuva = A0;
const int Porta_Informacao_Analogica_Umidade_Solo = A1;
const int Porta_Informacao_Digital_Umidade_Temperatura_Ar = 4;
DS1302 rtc(8, 9, 10);

// --- Objetos ---
DHT dht(Porta_Informacao_Digital_Umidade_Temperatura_Ar, DHT11);


void setup() {
  dht.begin();
  rtc.writeProtect(false);
  rtc.halt(false);
  //rtc.setDate(24, 6, 2025);  //Define o horário
  //rtc.setTime(15, 21, 0);      //Define o horário
  pinMode(buzzer, OUTPUT);
  Serial.begin(9600);
  Serial.println(">>>      SWMAI - Sistema de Monitoramento Iniciado       <<<");
}

void loop() {

  // --- Ínicio do bloco do módulo de relógio (RTC) ---
  String Hora_Atual = rtc.getTimeStr();
  String Data_Atual = rtc.getDateStr();
  // --- Fim do bloco do módulo de relógio (RTC) ---


  // --- Início do bloco de leitura e média dos sensores analógicos ---
  float Soma_Informacao_Chuva = 0;
  float Soma_Informacao_Umidade_Solo = 0;

  for (int Numero_Captura_Dados = 0; Numero_Captura_Dados < 30; Numero_Captura_Dados++) {
    int Informacao_Analogica_Chuva = analogRead(Porta_Informacao_Analogica_Chuva);
    Informacao_Analogica_Chuva = map(Informacao_Analogica_Chuva, Valor_Minimo_Chuva, Valor_Maximo_Chuva, 0, 100);
    Informacao_Analogica_Chuva = constrain(Informacao_Analogica_Chuva, 0, 100);
    Soma_Informacao_Chuva += Informacao_Analogica_Chuva;

    int Informacao_Analogica_Umidade_Solo = analogRead(Porta_Informacao_Analogica_Umidade_Solo);
    Informacao_Analogica_Umidade_Solo = map(Informacao_Analogica_Umidade_Solo, Valor_Minimo_Umidade_Solo, Valor_Maximo_Umidade_Solo, 0, 100);
    Informacao_Analogica_Umidade_Solo = constrain(Informacao_Analogica_Umidade_Solo, 0, 100);
    Soma_Informacao_Umidade_Solo += Informacao_Analogica_Umidade_Solo;

    delay(100);
  }

  float Media_Informacao_Chuva = Soma_Informacao_Chuva / 30.0;
  float Media_Informacao_Umidade_Solo = Soma_Informacao_Umidade_Solo / 30.0;
  // --- Fim do bloco de leitura e média dos sensores analógicos ---


  // --- Classificação dos status ---
  String Status_Chuva;
  if (Media_Informacao_Chuva == 0) {
    Status_Chuva = "Seco";
  } else if (Media_Informacao_Chuva > 0 && Media_Informacao_Chuva <= 20) {
    Status_Chuva = "Chuvisco / Chuva Fraca";
  } else if (Media_Informacao_Chuva > 20 && Media_Informacao_Chuva <= 50) {
    Status_Chuva = "Chuva Moderada";
  } else if (Media_Informacao_Chuva > 50 && Media_Informacao_Chuva <= 85) {
    Status_Chuva = "Chuva Forte";
  } else if (Media_Informacao_Chuva > 85 && Media_Informacao_Chuva <= 95) {
    Status_Chuva = "Chuva Muito Forte";
  } else {
    Status_Chuva = "Chuva Torrencial";
  }

  String Status_Umidade_Solo;
  if (Media_Informacao_Umidade_Solo <= 10) {
    Status_Umidade_Solo = "Muito Seco (Crítico)";
  } else if (Media_Informacao_Umidade_Solo > 10 && Media_Informacao_Umidade_Solo <= 30) {
    Status_Umidade_Solo = "Seco (Precisa de água)";
  } else if (Media_Informacao_Umidade_Solo > 30 && Media_Informacao_Umidade_Solo <= 70) {
    Status_Umidade_Solo = "Úmido (Ideal)";
  } else if (Media_Informacao_Umidade_Solo > 70 && Media_Informacao_Umidade_Solo <= 90) {
    Status_Umidade_Solo = "Molhado (Excesso de água)";
  } else {
    Status_Umidade_Solo = "Encharcado (Excesso de água)";
  }

  String Status_Umidade_Ar;
  String Status_Temperatura_Ar;
  float Leitura_Umidade_Ar = dht.readHumidity();
  float Leitura_Temperatura_Ar = dht.readTemperature();

  if (isnan(Leitura_Umidade_Ar) || isnan(Leitura_Temperatura_Ar)) {
    Status_Umidade_Ar = "Erro de Leitura";
    Status_Temperatura_Ar = "Erro de Leitura";
    Leitura_Umidade_Ar = 0;
    Leitura_Temperatura_Ar = 0;
  } else {
    if (Leitura_Umidade_Ar < 30) {
      Status_Umidade_Ar = "Baixa";
    } else if (Leitura_Umidade_Ar <= 60) {
      Status_Umidade_Ar = "Confortável";
    } else if (Leitura_Umidade_Ar <= 80) {
      Status_Umidade_Ar = "Alta";
    } else {
      Status_Umidade_Ar = "Muito Alta (Crítica)";
    }

    if (Leitura_Temperatura_Ar < 15) {
      Status_Temperatura_Ar = "Frio";
    } else if (Leitura_Temperatura_Ar < 25) {
      Status_Temperatura_Ar = "Ameno / Agradável";
    } else if (Leitura_Temperatura_Ar < 32) {
      Status_Temperatura_Ar = "Quente";
    } else {
      Status_Temperatura_Ar = "Muito Quente";
    }
  }
  // --- Fim da classificação dos status ---


  // --- Início da amostragem de dados ---
  Serial.println("----------------------------------------------------");
  Serial.println("                 PAINEL DE CONTROLE SWMAI");
  Serial.print("                 ");
  Serial.print(Data_Atual);
  Serial.print(" | ");
  Serial.print(Hora_Atual);
  Serial.println();
  Serial.println("----------------------------------------------------");

  Serial.print("  -> Temperatura do Ar: ");
  Serial.print(Status_Temperatura_Ar);
  Serial.print(" (");
  Serial.print(Leitura_Temperatura_Ar, 1);
  Serial.println(" ºC)");

  Serial.print("  -> Umidade do Ar: ");
  Serial.print(Status_Umidade_Ar);
  Serial.print(" (");
  Serial.print(Leitura_Umidade_Ar, 1);
  Serial.println("%)");

  Serial.print("  -> Chuva: ");
  Serial.print(Status_Chuva);
  Serial.print(" (");
  Serial.print(Media_Informacao_Chuva, 1);
  Serial.println("%)");

  Serial.print("  -> Umidade do Solo: ");
  Serial.print(Status_Umidade_Solo);
  Serial.print(" (");
  Serial.print(Media_Informacao_Umidade_Solo, 1);
  Serial.println("%)");

  Serial.println("----------------------------------------------------");
  Serial.println();
  // --- Fim da amostragem de dados ---

  // --- Início do toque do buzzer ---
  digitalWrite(buzzer, HIGH);
  delay(50);
  digitalWrite(buzzer, LOW);
  // --- Fim do toque do buzzer ---

  delay(2000);
}