#include <arm.h>

// ---------------------------------------------------------------------
// Define
// ---------------------------------------------------------------------
#define LED 13
#define ATIM_NOT_RESET 8
// ---------------------------------------------------------------------
// Global variables
// ---------------------------------------------------------------------
//Instance of  the class Arm
Arm myArm;
//The message to send at Sigfox or Lora
uint8_t msg[] = "Hello world!";

// ---------------------------------------------------------------------
// Implemented functions
// ---------------------------------------------------------------------

void setup()
{
  pinMode(ATIM_NOT_RESET,OUTPUT);
  digitalWrite(ATIM_NOT_RESET,HIGH);
	//Init Led for show error
	pinMode(LED, OUTPUT);
	digitalWrite(LED, LOW);
	
	//Init Arm and set LED to on if error
	if (myArm.Init(&Serial) != ARM_ERR_NONE)
		digitalWrite(LED, HIGH);
		
	//Get the Arm type
	armType_t armType;
	myArm.Info(&armType, 4, 0xB370, 868, 14);
	
	myArm.SetMode(ARM_MODE_LORAWAN);

	//Set Lora mode in uplink.
	  myArm.LwSetConfirmedFrame(5);
    myArm.LwIds(0xA000A523,0x70B3D59BA000A523,0x70B3D59BA0000004,0x3BC0BBBC75C55FD9F5A41AA255A16D0B,0xD951132E3FEED709ED6531FBA09AA0A2,0xEC0FA432F1A7C348522615CA0C0F5047);
    myArm.LwEnableOtaa(false);
    myArm.LwEnableRxWindows(true);
    myArm.LwEnableTxAdaptiveSpeed(true);
    myArm.LwEnableDutyCycle(false);
    myArm.LwEnableTxAdaptiveChannel(true);
    myArm.LwEnableRx2Adaptive(true);
    
    //Update the configuration into the arm.
	myArm.UpdateConfig();
}

void loop()
{	
	//Send the message to Sigfox or Lora
	myArm.Send(msg,sizeof(msg)-1);
	
	delay(15000);
}
