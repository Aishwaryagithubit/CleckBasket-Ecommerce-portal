/**
 * CleckBasket — RFID Card Reader
 * Hardware : Arduino Uno + MFRC522 module
 * Library  : MFRC522 by GithubCommunity (install via Arduino Library Manager)
 *
 * Wiring (MFRC522 → Arduino Uno):
 *   SDA  (SS)  →  Pin 10
 *   SCK        →  Pin 13
 *   MOSI       →  Pin 11
 *   MISO       →  Pin 12
 *   RST        →  Pin 9
 *   3.3V       →  3.3V   ← MUST be 3.3V, NOT 5V
 *   GND        →  GND
 *
 * Output over Serial (9600 baud):
 *   When card tapped  → "CARD:A3 38 A7 13"
 *   On startup        → "RFID_READY"
 */

#include <SPI.h>
#include <MFRC522.h>

#define SS_PIN   10
#define RST_PIN   9
#define LED_PIN  13   // built-in LED — blinks on successful read

MFRC522 mfrc522(SS_PIN, RST_PIN);

void setup() {
    Serial.begin(9600);
    SPI.begin();
    mfrc522.PCD_Init();

    pinMode(LED_PIN, OUTPUT);
    digitalWrite(LED_PIN, LOW);

    Serial.println("RFID_READY");
}

void loop() {
    /* Wait for a new card */
    if (!mfrc522.PICC_IsNewCardPresent()) return;
    if (!mfrc522.PICC_ReadCardSerial())   return;

    /* Build UID string: "A3 38 A7 13" */
    String uid = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
        if (i > 0) uid += " ";
        if (mfrc522.uid.uidByte[i] < 0x10) uid += "0";
        uid += String(mfrc522.uid.uidByte[i], HEX);
    }
    uid.toUpperCase();

    /* Flash the built-in LED */
    digitalWrite(LED_PIN, HIGH);
    delay(150);
    digitalWrite(LED_PIN, LOW);

    /* Send UID to serial bridge */
    Serial.println("CARD:" + uid);

    /* Halt so the same card isn't read twice */
    mfrc522.PICC_HaltA();
    mfrc522.PCD_StopCrypto1();

    delay(1500);  /* debounce — wait 1.5 s before next read */
}
