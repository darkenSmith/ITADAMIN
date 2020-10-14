<?php
/*----------------------------------------------------------------------------------------------------
Change Log
Date			tag						Ticket				By						Description
------------------------------------------------------------------------------------------------------
22/08/18	Created											Alex.Smith		Created
07/09/18	structure										Neil.Baker		Add SQL for adding structures to recyc DB
------------------------------------------------------------------------------------------------------*/

/*----------------------------------------------------------------------------------------------------
SQL Updates
------------------------------------------------------------------------------------------------------
INSERT INTO `recyc_structure` (`id`, `controller`, `action`, `friendly`, `layout`, `active`, `unrestricted`, `container`, `child_of`) VALUES (NULL, 'RS', 'booking', NULL, '1', '1', '1', 'container', NULL);
INSERT INTO `recyc_structure` (`id`, `controller`, `action`, `friendly`, `layout`, `active`, `unrestricted`, `container`, `child_of`) VALUES (NULL, 'RS', 'detialdoc', NULL, '1', '1', '1', 'container', NULL);
INSERT INTO `recyc_structure` (`id`, `controller`, `action`, `friendly`, `layout`, `active`, `unrestricted`, `container`, `child_of`) VALUES (NULL, 'RS', 'arc', NULL, '1', '1', '1', 'container', NULL);
INSERT INTO `recyc_structure` (`id`, `controller`, `action`, `friendly`, `layout`, `active`, `unrestricted`, `container`, `child_of`) VALUES (NULL, 'RS', 'updatadata', NULL, '1', '1', '1', 'container', NULL);
INSERT INTO `recyc_structure` (`id`, `controller`, `action`, `friendly`, `layout`, `active`, `unrestricted`, `container`, `child_of`) VALUES (NULL, 'RS', 'isemail', NULL, '0', '1', '1', 'container', NULL);
INSERT INTO `recyc_structure` (`id`, `controller`, `action`, `friendly`, `layout`, `active`, `unrestricted`, `container`, `child_of`) VALUES (NULL, 'RS', 'isPdf', NULL, '0', '1', '1', 'container', NULL);
INSERT INTO `recyc_structure` (`id`, `controller`, `action`, `friendly`, `layout`, `active`, `unrestricted`, `container`, `child_of`) VALUES (NULL, 'RS', 'isdone', NULL, '0', '1', '1', 'container', NULL);
INSERT INTO `recyc_structure` (`id`, `controller`, `action`, `friendly`, `layout`, `active`, `unrestricted`, `container`, `child_of`) VALUES (NULL, 'RS', 'Ordcheck', NULL, '1', '1', '1', 'container', NULL);
INSERT INTO `recyc_structure` (`id`, `controller`, `action`, `friendly`, `layout`, `active`, `unrestricted`, `container`, `child_of`) VALUES (NULL, 'RS', 'BookingLog', NULL, '1', '1', '1', 'container', NULL);
INSERT INTO `recyc_structure` (`id`, `controller`, `action`, `friendly`, `layout`, `active`, `unrestricted`, `container`, `child_of`) VALUES (NULL, 'RS', 'UpdateAPS', NULL, '1', '1', '1', 'container', NULL);
INSERT INTO `recyc_structure` (`id`, `controller`, `action`, `friendly`, `layout`, `active`, `unrestricted`, `container`, `child_of`) VALUES (NULL, 'RS', 'RGR', NULL, '1', '1', '1', 'container', NULL);

INSERT INTO `recyc_permissions` (`role_id`, `structure_id`) VALUES ('1', '38'), ('1', '39'), ('1', '40'), ('1', '41'), ('1', '42'), ('1', '43'), ('1', '44'), ('1', '45'), ('1', '46'), ('1', '47'), ('1', '48'), ('2', '38'), ('2', '39'), ('2', '40'), ('2', '41'), ('2', '42'), ('2', '43'), ('2', '44'), ('2', '45'), ('2', '46'), ('2', '47'), ('2', '48');
------------------------------------------------------------------------------------------------------*/
namespace App\Controllers;

use App\Models\User;
use App\Models\Conf\Confirm;


class ConfController {

	
	public function thankyoucust(){
		$data		= new user();
		$data->getRoles();
		$roles	= $data->roles;

		$data->getCustomers();
		$customers = $data->customers;
		$this->template->view('RECBooking/pages/thankyou', $this->getCommonData());
		//require_once('./RECbooking/thankyou.php');
	}


	//17/12/2018
	public function confmulti(){
		$data		= new user();
		$data->getRoles();
		$roles	= $data->roles;
		$confdata = new Confirm();
		$confirm = $confdata->confirmlist();

		$data->getCustomers();
		$customers = $data->customers;
		//require_once( './RECbooking/confirmmutli.php');
	}

		
	public function unconfmulti(){
		$data		= new user();
		$data->getRoles();
		$roles	= $data->roles;

		$data->getCustomers();
		$customers = $data->customers;
		$this->template->view('RECBooking/pages/unconfirmmutli', $this->getCommonData());
		//require_once( './RECbooking/unconfirmmutli.php');
	}

	public function unbookmulti(){
		$data		= new user();
		$data->getRoles();
		$roles	= $data->roles;

		$data->getCustomers();
		$customers = $data->customers;
		$this->template->view('RECBooking/pages/unbookreq', $this->getCommonData());
		//require_once( './RECbooking/unbookreq.php');
	}

	private function getCommonData()
    {
        $data = new User();
        $data->getRoles();
        $roles = $data->roles;

        $data->getCustomers();
        $customers = $data->customers;

        return [
            'customers' => $customers,
            'data' => $data,
            'roles' => $roles,
        ];


    }
/////////
}
