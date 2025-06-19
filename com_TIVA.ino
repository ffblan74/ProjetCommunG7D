const int servoPin = PA_2;
unsigned long previousMicros = 0;
const unsigned long period = 20000;
int newValue = 0;

void setup() {
  pinMode(servoPin, OUTPUT);
  digitalWrite(servoPin, LOW);
  Serial.begin(9600); 
  moveServo(90);
}
void loop() {if (Serial.available() > 0) {
    String command = Serial.readStringUntil('\n');

    if (command=="1"){
      moveServo(50);
      moveServo(90);
    }
    else{
      moveServo(130);
      moveServo(90);
    }
  }
}

void moveServo(int angle) {
  if(angle < 0) angle = 0;
  if(angle > 180) angle = 180;
  int pulseWidth = map(angle, 0, 180, 1000, 2000);
  
  for(int i = 0; i < 30; i++) {
    generatePWM(pulseWidth);
  }
}

void generatePWM(int pulseWidthMicros) {
  digitalWrite(servoPin, HIGH);
  delayMicroseconds(pulseWidthMicros);
  
  digitalWrite(servoPin, LOW);
  delayMicroseconds(period - pulseWidthMicros);
}
