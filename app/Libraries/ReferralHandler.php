<?php
namespace App\Libraries;

use App\Models\ReferralModel;
use Exception;

class ReferralHandler
{
    protected $model;

    public function __construct()
    {
        $this->model = new ReferralModel();
    }

    public function receive($transmitData)
    {
        try {
            $input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($transmitData));
            $data = json_decode($input, true);

            if (empty($data['LogID'])) {
                return $this->responseMessage(403, 'Reference ID is Invalid, must be numeric and non-empty');
            }

            if (empty($data['receivedDate'])) {
                return $this->responseMessage(403, 'Received date is Invalid, must be a valid date');
            }

            if (empty($data['receivedPerson']) || trim($data['receivedPerson']) === '') {
                return $this->responseMessage(403, 'Received person is Invalid, must not be empty');
            }

            $param = [
                'LogID' => $data['LogID'],
                'receivedDate' => date("Y-m-d H:i:s", strtotime($data['receivedDate'])),
                'receivedPerson' => $data['receivedPerson']
            ];

            $this->model->insertTrack($param);

            return $this->responseMessage(200, 'Patient received', $data['LogID']);
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }

    public function admit($transmitData)
    {
        try {
            $input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($transmitData));
            $data = json_decode($input, true);

            if (empty($data['LogID'])) {
                return $this->responseMessage(403, 'Reference ID is Invalid, must be numeric and non-empty');
            }

            if (empty($data['date'])) {
                return $this->responseMessage(403, 'Admit date is Invalid, must be a valid date');
            }

            if (empty($data['disp']) || trim($data['disp']) === '') {
                return $this->responseMessage(403, 'Admit disposition is Invalid, must not be empty');
            }

            $param = [
                'LogID' => $data['LogID'],
                'admDate' => date("Y-m-d H:i:s", strtotime($data['date'])),
                'admDisp' => $data['disp']
            ];

            $this->model->admiPatient($data['LogID'], $param);
            return $this->responseMessage(200, 'Patient is now admitted', $data['LogID']);
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }

    public function getDischargeData($transmitData) {
        try {
        
            // Ensure proper UTF-8 encoding
            $input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($transmitData));
            $data = json_decode($input, true);

            // Validate that LogID is set, numeric, and non-empty
            if (empty($data['LogID'])) {
                $response = array(
                    'code' => '403',
                    'response' => 'Reference ID is Invalid, must be numeric and non-empty',
                    'date' => date('m/d/Y H:i:s')
                );
                return json_encode($response);
            }

            // Fetch discharge data from model
            $dischargeData = $this->model->getdischargeInformation($data['LogID']);

            // Check if data was successfully retrieved
            if (empty($dischargeData)) {
                $response = array(
                    'code' => '404',
                    'response' => 'No discharge data found for the given LogID',
                    'date' => date('m/d/Y H:i:s')
                );
                return json_encode($response);
            }

            // Return the retrieved discharge data
            return $this->responseMessage(200, 'Patient is now discharged', $data['LogID']);
        } 
        catch (Exception $exception) {
            // Handle any exceptions that occur during the process
            return $this->errorResponse($exception);
        }
    }

    private function responseMessage($code, $message, $logID = null)
    {
        $response = [
            'code' => $code,
            'response' => $message,
            'date' => date('m/d/Y H:i:s')
        ];
        
        if ($logID) {
            $response['LogID'] = $logID;
        }

        return json_encode($response);
    }

    private function errorResponse($exception)
    {
        return json_encode([
            'code' => 500,
            'response' => 'An error occurred: ' . $exception->getMessage(),
            'date' => date('m/d/Y H:i:s')
        ]);
    }
}
