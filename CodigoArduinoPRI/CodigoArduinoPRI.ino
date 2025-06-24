/* PROJETO SWMAI 
Gustavo Veronezi de Carvalho
Julio Cesar Teiche Fraioli
*/

// --- Valores de calibração ---
const int Valor_Minimo_Chuva = 750;
const int Valor_Maximo_Chuva = 0;
const int Valor_Minimo_Umidade_Solo = 750;
const int Valor_Maximo_Umidade_Solo = 0;

// --- Portas ---
const int buzzer = 12;
const int Porta_Informacao_Analogica_Chuva = A0;
const int Porta_Informacao_Analogica_Umidade_Solo = A1;

void setup() {
  pinMode(buzzer, OUTPUT);
  Serial.begin(9600);
  Serial.println(">>> SWMAI - Sistema de Monitoramento Iniciado <<<");
}

void loop() {

  // --- Ínicio do bloco de sensor de Chuva ---
  float Soma_Informacao_Chuva = 0;
  for (int Numero_Captura_Dados = 0; Numero_Captura_Dados < 30; Numero_Captura_Dados++) {

    int Informacao_Analogica_Chuva = analogRead(Porta_Informacao_Analogica_Chuva);

    Informacao_Analogica_Chuva = map(Informacao_Analogica_Chuva, Valor_Minimo_Chuva, Valor_Maximo_Chuva, 0, 100);
    Informacao_Analogica_Chuva = constrain(Informacao_Analogica_Chuva, 0, 100);
    Soma_Informacao_Chuva += Informacao_Analogica_Chuva;

    delay(100);
  }

  float Media_Informacao_Chuva = Soma_Informacao_Chuva / 30.0;

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
  // --- Fim do bloco de sensor de Chuva ---

  // --- Início do bloco de sensor de umidade do solo ---
  float Soma_Informacao_Umidade_Solo = 0;

  for (int Numero_Captura_Dados = 0; Numero_Captura_Dados < 30; Numero_Captura_Dados++) {

    int Informacao_Analogica_Umidade_Solo = analogRead(Porta_Informacao_Analogica_Umidade_Solo);

    Informacao_Analogica_Umidade_Solo = map(Informacao_Analogica_Umidade_Solo, Valor_Minimo_Umidade_Solo, Valor_Maximo_Umidade_Solo, 0, 100);
    Informacao_Analogica_Umidade_Solo = constrain(Informacao_Analogica_Umidade_Solo, 0, 100);
    Soma_Informacao_Umidade_Solo += Informacao_Analogica_Umidade_Solo;

    delay(100);
  }

  float Media_Informacao_Umidade_Solo = Soma_Informacao_Umidade_Solo / 30.0;

  String Status_Umidade_Solo;

  if (Media_Informacao_Umidade_Solo <= 10) {
    Status_Umidade_Solo = "Muito Seco (Crítico)";
  } else if (Media_Informacao_Umidade_Solo > 10 && Media_Informacao_Umidade_Solo <= 30) {
    Status_Umidade_Solo = "Seco (Precisa de água)";
  } else if (Media_Informacao_Umidade_Solo > 30 && Media_Informacao_Umidade_Solo <= 70) {
    Status_Umidade_Solo = "Úmido (Ideal)";
  } else if (Media_Informacao_Umidade_Solo > 70 && Media_Informacao_Umidade_Solo <= 90) {
    Status_Umidade_Solo = "Molhado";
  } else {
    Status_Umidade_Solo = "Encharcado (Excesso de água)";
  }
  // --- Fim do bloco de sensor de umidade do solo ---


  // --- Início da amostragem de dados ---
  Serial.println("------------------------------------------");
  Serial.println("       PAINEL DE CONTROLE SWMAI");
  Serial.println("------------------------------------------");

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

  Serial.println("------------------------------------------");
  Serial.println();

  // --- Fim da amostragem de dados ---

  digitalWrite(buzzer, HIGH);
  delay(50);
  digitalWrite(buzzer, LOW);

  delay(3000);
}