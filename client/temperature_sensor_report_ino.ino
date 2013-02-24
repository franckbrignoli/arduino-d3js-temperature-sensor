#include <Ethernet.h>
#include <SPI.h>

#define aref_voltage 3.3 

byte mac[] = { 0x90, 0xA2, 0xDA, 0x80, 0x0A, 0x18 };
int sensorPin = 1;

EthernetClient client;

void setup()
{
  Serial.begin(9600);
  analogReference(EXTERNAL);
  Ethernet.begin(mac);
  delay(1000);
}

void loop()
{
  int reading = analogRead(sensorPin);

  float voltage = reading * aref_voltage;
  voltage /= 1024.0;

  float temperature = (voltage - 0.5) * 100 ;
  
  char data[37] = "token=verycooltoken&temperature=";
  dtostrf(temperature, 1, 1, &data[32]);
  
  Serial.println("Trying to connect ...");
  if (client.connect("www.domain.com", 80)) {
    Serial.println("Send POST Request!");
    client.println("POST /api.php HTTP/1.1");
    client.println("Host: www.domain.com");
    client.println("Content-Type: application/x-www-form-urlencoded");
    client.println("Connection: close");
    client.print("Content-Length: ");
    client.println(37);
    client.println();
    client.print(data);
    client.println();    
  } else {
    Serial.println("Unable to connect");
  }    
  
  if (client.connected()) {
    Serial.println();
    Serial.println("disconnecting.");
    client.stop();
  }
  
  delay(60000);
}

