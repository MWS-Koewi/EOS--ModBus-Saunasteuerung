<?php

	require_once __DIR__ . '/SemaphoreHelper.php'; 
	
	class EOSModBus extends IPSModule
	{
		use \EOS\SemaphoreHelper;
		
		var $Modbus_Properties = array(
			array("name" => "Gerät Modelltyp",      "ident" => "eosModelType",          "modell" => "Info",  "varType" => 1,  "varProfile" => null,                       "address" => 0,   "varHasAction" => false),
			array("name" => "Firmwareversion",      "ident" => "eosFirmware",           "modell" => "Info",  "varType" => 1,  "varProfile" => null,                       "address" => 1,   "varHasAction" => false),
			array("name" => "Temperatur Istwert",   "ident" => "eosCurrentTemp",        "modell" => "",      "varType" => 1,  "varProfile" => "EOSModBus.Temperature2",   "address" => 4,   "varHasAction" => false),
			array("name" => "Feuchte Istwert",      "ident" => "eosCurrentHumidity",    "modell" => "Vapo",  "varType" => 1,  "varProfile" => "~Humidity",                "address" => 5,   "varHasAction" => false),
			array("name" => "Licht",                "ident" => "eosLightSwitch",        "modell" => "",      "varType" => 0,  "varProfile" => "~Switch",                  "address" => 100, "varHasAction" => true),
			array("name" => "Ofen",                 "ident" => "eosHeaterSwitch",       "modell" => "",      "varType" => 0,  "varProfile" => "~Switch",                  "address" => 101, "varHasAction" => true),
			array("name" => "Verdampfer",           "ident" => "eosVaporizerSwitch",    "modell" => "Vapo",  "varType" => 0,  "varProfile" => "~Switch",                  "address" => 102, "varHasAction" => true),
			array("name" => "Licht Sollwert",       "ident" => "eosSetLightValue",      "modell" => "",      "varType" => 1,  "varProfile" => "~Intensity.100",           "address" => 150, "varHasAction" => true),
			array("name" => "Temperatur Sollwert",  "ident" => "eosSetTempValue",       "modell" => "",      "varType" => 1,  "varProfile" => "EOSModBus.Temperature",    "address" => 151, "varHasAction" => true),
			array("name" => "Feuchte Sollwert",     "ident" => "eosSetHumidityValue",   "modell" => "Vapo",  "varType" => 1,  "varProfile" => "~Intensity.100",           "address" => 152, "varHasAction" => true)
		);
		
		public function Create()
		{
			//Never delete this line!
			parent::Create();
			
			// verbinde ein ModBus Gateway 
			$this->ConnectParent("{A5F663AB-C400-4FE5-B207-4D67CC030564}");
			
			// Erzeuge die eignen Profile
			$this->CreateVariableProfiles();

			//registriere die Eigenschaften für die Einstellungen
			$this->RegisterPropertyInteger('Vaporizer', 0);
			$this->RegisterPropertyInteger('Infos', 0);
			$this->RegisterPropertyBoolean('StatusEmulieren', true);
			$this->RegisterPropertyInteger('Interval', 0);
			$this->RegisterTimer('UpdateTimer', 0, 'EOS_RequestRead($_IPS["TARGET"]);');
        }

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
		}

		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
			
			// create variables
			foreach ($this->Modbus_Properties as $property) {
				if($property['modell'] == "Vapo" && $this->ReadPropertyInteger("Vaporizer") == 0) {
					@$this->DisableAction($property['ident']);
					$this->UnregisterVariable($property['ident']);
					continue;
				}
				
				if($property['modell'] == "Info" && $this->ReadPropertyInteger("Infos") == 0) {
					@$this->DisableAction($property['ident']);
					$this->UnregisterVariable($property['ident']);
					continue;
				}

				$var = @IPS_GetObjectIDByIdent($property['ident'], $this->InstanceID);
				if(!$var) {
					switch ($property['varType']) {
						case 0:
							if($property['varProfile'] != null && IPS_VariableProfileExists($property['varProfile'])) {
								$this->RegisterVariableBoolean($property['ident'], $property['name'], $property['varProfile']);
							}
							else {
								$this->RegisterVariableBoolean($property['ident'], $property['name']);
							}
							break;
						case 1:
							if($property['varProfile'] != null && IPS_VariableProfileExists($property['varProfile'])) {
								$this->RegisterVariableInteger($property['ident'], $property['name'], $property['varProfile']);
							}
							else {
								$this->RegisterVariableInteger($property['ident'], $property['name']);
							}
							break;
						case 2:
							if($property['varProfile'] != null && IPS_VariableProfileExists($property['varProfile'])) {
								$this->RegisterVariableFloat($property['ident'], $property['name'], $property['varProfile']);
							}
							else {
								$this->RegisterVariableFloat($property['ident'], $property['name']);
							}
							break;
						case 3:
							if($property['varProfile'] != null && IPS_VariableProfileExists($property['varProfile'])) {
								$this->RegisterVariableString($property['ident'], $property['name'], $property['varProfile']);
							}
							else {
								$this->RegisterVariableString($property['ident'], $property['name']);
							}
							break;
						}
				}
				if($property['varHasAction'])
					$this->EnableAction($property['ident']);
				else
					$this->DisableAction($property['ident']);
			}			

			if ($this->ReadPropertyInteger('Interval') > 0) {
				$this->SetTimerInterval('UpdateTimer', $this->ReadPropertyInteger('Interval'));
			} else {
				$this->SetTimerInterval('UpdateTimer', 0);
			}
		}

		public function RequestRead()
		{
			$Gateway = IPS_GetInstance($this->InstanceID)['ConnectionID'];
			if ($Gateway == 0) {
				$this->LogMessage("Problme mit dem Gareway", KL_WARNING);
				return false;
			}
			$IO = IPS_GetInstance($Gateway)['ConnectionID'];
			if ($IO == 0) {
				$this->LogMessage("I/O Verbindungsproblem", KL_WARNING);
				return false;
			}
			$Result = $this->ReadData();
			IPS_Sleep(100);
		}

		public function RequestAction($Ident, $Value) 
		{ 
			switch ($Ident) 
			{ 
				case 'eosSetTempValue':
					$this->SetTempValue($Value);
					break;
				case 'eosSetLightValue':
					$this->SetLightValue($Value);
					break;
				case 'eosSetHumidityValue':
					$this->SetHumidityValue($Value);
					break;
				case 'eosLightSwitch':
					$this->SetLightSwitch($Value);
					break;
				case 'eosHeaterSwitch':
					$this->SetHeaterSwitch($Value);
					break;
				case 'eosVaporizerSwitch':
					$this->SetVaporizerSwitch($Value);
					break;
				default:
					break; 
			}
		}
			
		public function SetLightValue(int $Value)
		{
			if($Value < 0) {$Value = 0;}
			if($Value > 100) {$Value = 100;}

			if ($this->ReadPropertyBoolean('StatusEmulieren') == true)
			{
				$this->SetValue('eosSetLightValue', $Value);
			}

			$this->WriteData(150, $Value);
		}

		public function SetTempValue(int $Value)
		{
			if($Value < 30) {$Value = 30;}
			if($Value > 115) {$Value = 115;}

			if ($this->ReadPropertyBoolean('StatusEmulieren') == true)
			{
				$this->SetValue('eosSetTempValue', $Value);
			}

			$this->WriteData(151, $Value);
		}

		public function SetHumidityValue(int $Value)
		{
			if($Value < 0) {$Value = 0;}
			if($Value > 100) {$Value = 100;}

			if ($this->ReadPropertyBoolean('StatusEmulieren') == true)
			{
				$this->SetValue('eosSetHumidityValue', $Value);
			}

			$this->WriteData(152, $Value);
		}

		public function SetLightSwitch(bool $Value)
		{
			if($Value < 0) {$Value = 0;}
			if($Value > 1) {$Value = 1;}

			if ($this->ReadPropertyBoolean('StatusEmulieren') == true)
			{
				$this->SetValue('eosLightSwitch', $Value);
			}

			$this->WriteData(100, $Value);
		}

		public function SetHeaterSwitch(bool $Value)
		{
			if($Value < 0) {$Value = 0;}
			if($Value > 1) {$Value = 1;}

			if ($this->ReadPropertyBoolean('StatusEmulieren') == true)
			{
				$this->SetValue('eosHeaterSwitch', $Value);
			}

			$this->WriteData(101, $Value);
		}

		public function SetVaporizerSwitch(bool $Value)
		{
			if($Value < 0) {$Value = 0;}
			if($Value > 1) {$Value = 1;}

			if ($this->ReadPropertyBoolean('StatusEmulieren') == true)
			{
				$this->SetValue('eosVaporizerSwitch', $Value);
			}

			$this->WriteData(102, $Value);
		}

		protected function CreateVariableProfiles() 
		{
			$profileName = "EOSModBus.Temperature";
			if(!IPS_VariableProfileExists($profileName)) {
				IPS_CreateVariableProfile($profileName, 1);
			}
			IPS_SetVariableProfileText($profileName, "", " °C");
			IPS_SetVariableProfileValues($profileName, 30, 115, 1);
			IPS_SetVariableProfileIcon($profileName,  "Temperature");
			
			$profileName = "EOSModBus.Temperature2";
			if(!IPS_VariableProfileExists($profileName)) {
				IPS_CreateVariableProfile($profileName, 1);
			}
			IPS_SetVariableProfileText($profileName, "", " °C");
			IPS_SetVariableProfileIcon($profileName,  "Temperature");
		}
	
		protected function ModulErrorHandler($errno, $errstr)
		{
			$this->SendDebug('ERROR', utf8_decode($errstr), 0);
			echo $errstr;
		}
	
		protected function SetValueExt($Variable, $Value)
		{
			$id = @$this->GetIDForIdent($Variable['ident']);
			$this->SetValue($Variable['ident'], $Value);
			return true;
		}
		
		private function WriteData($Address, $Value)
		{
			$DataID = "{E310B701-4AE7-458E-B618-EC13A1A6F6A8}"; 
			$Function = 6;
			$Quantity = 2;
			$Data = pack('n', $Value );

			set_error_handler([$this, 'ModulErrorHandler']);
			$response = $this->SendDataToParent(json_encode(Array("DataID" => $DataID, "Function" => $Function, "Address" => $Address , "Quantity" => $Quantity, "Data" => $Data)));
			restore_error_handler();
			
			return true;
		}
		private function ReadData()
		{
			foreach ($this->Modbus_Properties as $Variable) {
				
				if($Variable['modell'] == "Vapo" && $this->ReadPropertyInteger("Vaporizer") == 0) {
					continue;
				}					
					
				if($Variable['modell'] == "Info" && $this->ReadPropertyInteger("Infos") == 0) {
					continue;
				}					

				$DataID = "{E310B701-4AE7-458E-B618-EC13A1A6F6A8}"; 
				$address = $Variable['address'];
				$Function = 3;
				$Quantity = 1;
				$Data = "";
							
				set_error_handler([$this, 'ModulErrorHandler']);
				$response = $this->SendDataToParent(json_encode(Array("DataID" => $DataID, "Function" => $Function, "Address" => $address , "Quantity" => $Quantity, "Data" => $Data)));
				restore_error_handler();
	
				if ($response === false) {
					$this->LogMessage("Keine Daten", KL_MESSAGE);
				}
				else {
					$ReadValue = substr($response, 2);
					$Value = unpack('n', $ReadValue)[1];
					$this->SetValueExt($Variable, $Value);
				}
			}
			return true;
		}
	}
