<?php


namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Logger;
use Exception;

set_time_limit(0);
/**
 * Class OrderSync
 * @package App\Models
 */
class OrderSync extends AbstractModel
{

    public $orders;
    public $updateorders;
    public $companies;
    public $newOrders = [];
    public $upOrders = [];

    /**
     * OrderSync constructor.
     */
    public function __construct()
    {
        $this->gdb = Database::getInstance('greenoak');
        parent::__construct();
    }

    public function start()
    {
        Logger::getInstance("OrderSync.log")->info(
            'start',
            []
        );
        $date = new \DateTime();
        $date->modify('-5 year');
        $from = $date->format('Y-m-d H:i:s');

        //get all orders in last 3 months, check if they belong to a portal customer and if they do, import
        $sql = "SELECT
			distinct(SO.SalesOrderNumber) as sales_order_number,
			Cast(SO.CompanyID AS NVARCHAR(max)) as company_id,
			Cast(DELPROD.SalesOrderID AS NVARCHAR(max)) as sales_order_id
		FROM
			[DeliveryProductDetail] DELPROD
		RIGHT OUTER JOIN
			SalesOrders SO ON DELPROD.SalesOrderID = SO.SalesOrderID
		WHERE
			DELPROD.SalesOrderID IN
				(Select SalesOrderId FROM SalesOrders WHERE CompanyId IN
					(SELECT CompanyID FROM Company )
				AND OrderPlacedDate > :from
				)
		";

        try {
            $result = $this->gdb->prepare($sql);
            $result->execute(array(':from' => $from));
            $this->orders = $result->fetchAll(\PDO::FETCH_OBJ);
            Logger::getInstance("OrderSync.log")->debug(
                'start-orders',
                [$this->orders]
            );
        } catch (\Exception $e) {
            Logger::getInstance("OrderSync.log")->error(
                'start-error',
                [$e->getLine(), $e->getMessage()]
            );
        }

        
        //$this-> upprocess();
    }
    public function upprocess()
    {
        Logger::getInstance("OrderSync1.log")->info(
            'upprocess',
            []
        );

        $sql = 'SELECT * FROM recyc_order_information WHERE waste_transfer_number is null and sales_order_id is not null';
        $result = $this->rdb->prepare($sql);
        $result->execute();
        $data = $result->fetchAll(\PDO::FETCH_OBJ);

        foreach ($data as $order) {

            $sql = "SELECT
			distinct(SO.SalesOrderNumber) as sales_order_number,
			Cast(SO.CompanyID AS NVARCHAR(max)) as company_id,
			Cast(DELPROD.SalesOrderID AS NVARCHAR(max)) as sales_order_id
		FROM
			[DeliveryProductDetail] DELPROD
		RIGHT OUTER JOIN
			SalesOrders SO ON DELPROD.SalesOrderID = SO.SalesOrderID
		WHERE
			DELPROD.SalesOrderID IN
				(Select SalesOrderId FROM SalesOrders WHERE CompanyId IN
					(SELECT CompanyID FROM Company )
				AND SalesOrderId = :SalesOrderId
				)
		";

        try {
            $result = $this->gdb->prepare($sql);
            $result->execute(array(':SalesOrderId' => $order->sales_order_id));
            $this->updateorders = $result->fetch(\PDO::FETCH_OBJ);
            Logger::getInstance("OrderSynclog.log")->debug(
                'start-orders',
                [$this->updateorders]
            );
        } catch (\Exception $e) {
            Logger::getInstance("OrderSynclogfail.log")->error(
                'start-error',
                [$e->getLine(), $e->getMessage()]
            );
        }
           if($this->updateorders){

           

            Logger::getInstance("orderids.log")->info(
                'order-ids',
                [$this->updateorders->sales_order_id]
            );
            try{

                        $sql = "(
                            SELECT
                                Cast(SO.CompanyID AS NVARCHAR(max))  as 'company_id',
                                Cast(SO.SalesOrderID AS NVARCHAR(max))  as 'sales_order_id',
                                SO.SalesOrderNumber as 'sales_order_number',
                                DEL.WasteTransferNumber as 'waste_transfer_number',
                                A.LocationName  as 'location_name',
                                Cast(DELLOC.PickupLocationID AS NVARCHAR(max)) as 'location_id',
                                A.Address1 as 'address1',
                                A.Address2 as 'address2',
                                A.Address3 as 'address3',
                                A.Address4 as 'address4',
                                A.Town as 'town',
                                A.PostCode as 'postcode',
                                COALESCE(B.RegionName,'') AS 'county',
                                C.CountryName AS 'country',
                                A.TelNumber as 'telephone',
                                A.SiteCode as 'sitecode',
                                A.SICCode as 'sic_code',
                                DEL.ActualDeliveryDate as 'actual_delivery_date'
                            FROM
                                [GREENOAK].[We3Recycler].[dbo].[SalesOrders] SO
                            LEFT OUTER JOIN
                                [GREENOAK].[We3Recycler].[dbo].Delivery DEL ON DEL.SalesOrderID = SO.SalesOrderID
                            LEFT OUTER JOIN
                                [GREENOAK].[We3Recycler].[dbo].DeliveryLocations DELLOC ON DEL.DeliveryID = DELLOC.DeliveryID
                            LEFT OUTER JOIN
                                [GREENOAK].[We3Recycler].[dbo].PickUpLocations A ON DELLOC.PickupLocationID = A.PickUpLocationID
                            LEFT OUTER JOIN
                                [GREENOAK].[We3Recycler].[dbo].Region B ON A.County = B.RegionID
                            LEFT OUTER JOIN
                                [GREENOAK].[We3Recycler].[dbo].Country C ON A.Country = C.CountryID
                            WHERE
                                DELLOC.PickUpLocationID NOT IN ('00000000-0000-0000-0000-000000000000')
                                AND SO.SalesOrderID = :salesorderid2)
                                UNION
                                    (
                                        SELECT
                                          Cast(SO.CompanyID AS NVARCHAR(max)) as 'company_id',
                                          Cast(SO.SalesOrderID AS NVARCHAR(max)) as 'sales_order_id',
                                          SO.SalesOrderNumber as 'sales_order_number',
                                          DEL.WasteTransferNumber as 'waste_transfer_number',
                                          A.CompanyName as 'location_name',
                                          Cast(DELLOC.PickupLocationID AS NVARCHAR(max)) as 'location_id',
                                          A.PrimaryAddressLine1 AS address1,
                                          A.PrimaryAddressLine2 AS address2,
                                          A.PrimaryAddressLine3 AS address3,
                                          A.PrimaryAddressLine4 AS address4,
                                          A.PrimaryAddressTown AS town,
                                          A.PrimaryAddressPostCode AS postcode,
                                          COALESCE(B.RegionName,'') AS 'county',
                                          C.CountryName AS 'country',
                                          A.Telephone As telephone,
                                          A.SiteCode as sitecode,
                                          A.SICCode as sic_code,
                                          DEL.ActualDeliveryDate as actual_delivery_date
                                      FROM
                                            [GREENOAK].[We3Recycler].[dbo].[SalesOrders] SO
                                      LEFT OUTER JOIN
                                            Company A ON SO.CompanyID = A.CompanyID
                                      LEFT OUTER JOIN
                                            Region B ON A.PrimaryAddressCounty = B.RegionID
                                      LEFT OUTER JOIN
                                            Country C ON A.PrimaryAddressCountry = C.CountryID
                                      LEFT OUTER JOIN
                                            Delivery DEL ON DEL.SalesOrderID = SO.SalesOrderID
                                      LEFT OUTER JOIN
                                            DeliveryLocations DELLOC ON DEL.DeliveryID = DELLOC.DeliveryID
                                      LEFT OUTER JOIN
                                            PickUpLocations D ON DELLOC.PickupLocationID = D.PickUpLocationID
                                      WHERE
                                            DELLOC.PickUpLocationID IN ('00000000-0000-0000-0000-000000000000'
                                    )
                                AND SO.SalesOrderID = :salesorderid
                            )
                            ";
            
                            try {
                                $result = $this->gdb->prepare($sql);
                                $result->execute(array(':salesorderid' => $this->updateorders->sales_order_id, ':salesorderid2' => $this->updateorders->sales_order_id));
                                $this->updateorders->data = $result->fetch(\PDO::FETCH_OBJ);
                            } catch (\Exception $e) {
                                Logger::getInstance("OrderSyncgreen.log")->error(
                                    'Empty',
                                    [$e->getLine(), $e->getMessage()]
                                );
                            }
            
                                $sql = 'UPDATE recyc_order_information
                                set waste_transfer_number = :wtn,
                                location_name = :locname,
                                location_id = :locid, 
                                address1 = :add1,
                                address2 = :add2,
                                address3 = :add3,
                                address4 = :add4, 
                                town = :town, 
                                postcode = :postcode, 
                                county = :county,
                                country = :country,
                                telephone = :phone,
                                sitecode = :sitecode,
                                sic_code = :siccode, 
                                actual_delivery_date = :deldate
                                where sales_order_number = :salesOrderNumber';
            
                                $orderInfo = array( 
                                    ':salesOrderNumber' => $this->updateorders->data->sales_order_number,
                                    ':wtn' => $this->updateorders->data->waste_transfer_number,
                                    ':locname' => $this->updateorders->data->location_name,
                                    ':locid' => $this->updateorders->data->location_id,
                                    ':add1' => $this->updateorders->data->address1,
                                    ':add2' => $this->updateorders->data->address2,
                                    ':add3' => $this->updateorders->data->address3,
                                    ':add4' => $this->updateorders->data->address4,
                                    ':town' => $this->updateorders->data->town,
                                    ':postcode' => $this->updateorders->data->postcode,
                                    ':county' => $this->updateorders->data->county,
                                    ':country' => $this->updateorders->data->country,
                                    ':phone' => $this->updateorders->data->telephone,
                                    ':sitecode' => $this->updateorders->data->sitecode,
                                    ':siccode' => $this->updateorders->data->sic_code,
                                    ':deldate' => $this->updateorders->data->actual_delivery_date
                                );
            
                                $result = $this->rdb->prepare($sql);
                                $result->execute($orderInfo);
                                
                            
                            
                        }catch(Exception $e){
                            Logger::getInstance("updateerrOrderSync.log")->error(
                                'process-error',
                                [$e->getLine(), $e->getMessage()]
                            );
                        }
                    }
                }
        
    }
                        


    public function process()
    {
        Logger::getInstance("OrderSync1.log")->info(
            'process',
            []
        );

    

        foreach ($this->orders as $order) {

            
            // first check order information
            //waste_transfer_number
  
           

                try {
                    $sql = 'SELECT * FROM recyc_order_information WHERE sales_order_number = :order';
                    $result = $this->rdb->prepare($sql);
                    $result->execute(array(':order' => $order->sales_order_number));
                    $exists = $result->fetch(\PDO::FETCH_OBJ);
    
    
    
    
            if (!$exists) {
                    $this->newOrders[$order->sales_order_number] = $order;
                }
            } catch (\Exception $e) {
                Logger::getInstance("OrderSync.log")->error(
                    'process-error-foreach',
                    [$e->getLine(), $e->getMessage()]
                );
            }
        

        if (!empty($this->newOrders)) {
            Logger::getInstance("OrderSync.log")->debug(
                'this->newOrders count',
                [count($this->newOrders)]
            );
            // Display confirmation
            echo '
			<div class="alert alert-success fade-in" id="reset-container" >
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4>Order Sync Complete</h4>
        <p>' . count($this->newOrders) . ' Imported</p>
	    </div>';

            foreach ($this->newOrders as $newOrder) {
                //Get order items
                $sql = "SELECT
					Cast(SO.CompanyID AS NVARCHAR(max)) AS company_id,
					Cast(DELPROD.SalesOrderID AS NVARCHAR(max)) AS sales_order_id,
					SO.SalesOrderNumber AS sales_order_number,
					Cast(DELPROD.DeliveryProductDetailID AS NVARCHAR(max)) AS delivery_product_detail_id,
					PROD.ProductName AS product_name,
					DELPROD.SerialNumber AS serial_number,
					DELPROD.Quantity AS quantity,
					CASE WHEN WorkFlowDetailId = '99999999-9999-9999-9999-999999999999' THEN '1' ELSE '0' END AS 'processed',
					CAST(NetWeight AS DECIMAL (8,0)) AS weight
				FROM
					[DeliveryProductDetail] DELPROD
				INNER JOIN
					[Products] PROD ON DELPROD.ProductID = PROD.ProductID
				RIGHT OUTER JOIN
					ItrCurrent ITR ON DELPROD.DeliveryProductDetailID = ITR.DeliveryProductDetailId
				RIGHT OUTER JOIN
					SalesOrders SO ON DELPROD.SalesOrderID = SO.SalesOrderID
				WHERE
					DELPROD.SalesOrderID = :sales
				";

                try {
                    $result = $this->gdb->prepare($sql);
                    $result->execute(array(':sales' => $newOrder->sales_order_id));
                    $newOrder->items = $result->fetchAll(\PDO::FETCH_OBJ);
                } catch (\Exception $e) {
                    Logger::getInstance("OrderSync.log")->error(
                        'process-error',
                        [$e->getLine(), $e->getMessage()]
                    );
                }

                $sql = "SELECT * from recyc_company_sync WHERE greenoak_id = :greenoak";
                try {
                    $result = $this->rdb->prepare($sql);
                    $result->execute(array(':greenoak' => $newOrder->company_id));
                    $newOrder->company = $result->fetch(\PDO::FETCH_OBJ);
                } catch (\Exception $e) {
                    Logger::getInstance("OrderSync.log")->error(
                        'process-error',
                        [$e->getLine(), $e->getMessage()]
                    );
                }

                $sql = "(
				SELECT
					Cast(SO.CompanyID AS NVARCHAR(max))  as 'company_id',
					Cast(SO.SalesOrderID AS NVARCHAR(max))  as 'sales_order_id',
					SO.SalesOrderNumber as 'sales_order_number',
					DEL.WasteTransferNumber as 'waste_transfer_number',
					A.LocationName  as 'location_name',
					Cast(DELLOC.PickupLocationID AS NVARCHAR(max)) as 'location_id',
					A.Address1 as 'address1',
					A.Address2 as 'address2',
					A.Address3 as 'address3',
					A.Address4 as 'address4',
					A.Town as 'town',
					A.PostCode as 'postcode',
					COALESCE(B.RegionName,'') AS 'county',
					C.CountryName AS 'country',
					A.TelNumber as 'telephone',
					A.SiteCode as 'sitecode',
					A.SICCode as 'sic_code',
					DEL.ActualDeliveryDate as 'actual_delivery_date'
				FROM
					[GREENOAK].[We3Recycler].[dbo].[SalesOrders] SO
				LEFT OUTER JOIN
					[GREENOAK].[We3Recycler].[dbo].Delivery DEL ON DEL.SalesOrderID = SO.SalesOrderID
				LEFT OUTER JOIN
					[GREENOAK].[We3Recycler].[dbo].DeliveryLocations DELLOC ON DEL.DeliveryID = DELLOC.DeliveryID
				LEFT OUTER JOIN
					[GREENOAK].[We3Recycler].[dbo].PickUpLocations A ON DELLOC.PickupLocationID = A.PickUpLocationID
				LEFT OUTER JOIN
					[GREENOAK].[We3Recycler].[dbo].Region B ON A.County = B.RegionID
				LEFT OUTER JOIN
					[GREENOAK].[We3Recycler].[dbo].Country C ON A.Country = C.CountryID
				WHERE
					DELLOC.PickUpLocationID NOT IN ('00000000-0000-0000-0000-000000000000')
					AND SO.SalesOrderID = :salesorderid2)
					UNION
						(
							SELECT
						  	Cast(SO.CompanyID AS NVARCHAR(max)) as 'company_id',
						  	Cast(SO.SalesOrderID AS NVARCHAR(max)) as 'sales_order_id',
						  	SO.SalesOrderNumber as 'sales_order_number',
						  	DEL.WasteTransferNumber as 'waste_transfer_number',
						  	A.CompanyName as 'location_name',
						  	Cast(DELLOC.PickupLocationID AS NVARCHAR(max)) as 'location_id',
						  	A.PrimaryAddressLine1 AS address1,
						  	A.PrimaryAddressLine2 AS address2,
						  	A.PrimaryAddressLine3 AS address3,
						  	A.PrimaryAddressLine4 AS address4,
						  	A.PrimaryAddressTown AS town,
						  	A.PrimaryAddressPostCode AS postcode,
						  	COALESCE(B.RegionName,'') AS 'county',
						  	C.CountryName AS 'country',
						  	A.Telephone As telephone,
						  	A.SiteCode as sitecode,
						  	A.SICCode as sic_code,
						  	DEL.ActualDeliveryDate as actual_delivery_date
						  FROM
								[GREENOAK].[We3Recycler].[dbo].[SalesOrders] SO
						  LEFT OUTER JOIN
								Company A ON SO.CompanyID = A.CompanyID
						  LEFT OUTER JOIN
								Region B ON A.PrimaryAddressCounty = B.RegionID
						  LEFT OUTER JOIN
								Country C ON A.PrimaryAddressCountry = C.CountryID
						  LEFT OUTER JOIN
								Delivery DEL ON DEL.SalesOrderID = SO.SalesOrderID
						  LEFT OUTER JOIN
								DeliveryLocations DELLOC ON DEL.DeliveryID = DELLOC.DeliveryID
						  LEFT OUTER JOIN
								PickUpLocations D ON DELLOC.PickupLocationID = D.PickUpLocationID
						  WHERE
								DELLOC.PickUpLocationID IN ('00000000-0000-0000-0000-000000000000'
						)
					AND SO.SalesOrderID = :salesorderid
				)
				";

                try {
                    $result = $this->gdb->prepare($sql);
                    $result->execute(array(':salesorderid' => $newOrder->sales_order_id, ':salesorderid2' => $newOrder->sales_order_id));
                    $newOrder->data = $result->fetch(\PDO::FETCH_OBJ);
                } catch (\Exception $e) {
                    Logger::getInstance("OrderSync.log")->error(
                        'process-error',
                        [$e->getLine(), $e->getMessage()]
                    );
                }

                try {
                    $this->rdb->beginTransaction();

                    $sql = 'INSERT INTO recyc_order_information
						(company_id,sales_order_id,sales_order_number,waste_transfer_number,location_name,
						location_id,address1,address2,address3,address4,
						town,postcode,county,country,telephone,
						sitecode,sic_code,actual_delivery_date)
						VALUES
						(:company,:salesOrderId,:salesOrderNumber,:wtn,:locname,
						:locid, :add1, :add2, :add3, :add4,
						:town,:postcode,:county,:country,:phone,
						:sitecode,:siccode, :deldate)';

                    $orderInfo = array(
                        ':company' => $newOrder->company->company_id,
                        ':salesOrderId' => $newOrder->sales_order_id,
                        ':salesOrderNumber' => $newOrder->sales_order_number,
                        ':wtn' => $newOrder->data->waste_transfer_number,
                        ':locname' => $newOrder->data->location_name,
                        ':locid' => $newOrder->data->location_id,
                        ':add1' => $newOrder->data->address1,
                        ':add2' => $newOrder->data->address2,
                        ':add3' => $newOrder->data->address3,
                        ':add4' => $newOrder->data->address4,
                        ':town' => $newOrder->data->town,
                        ':postcode' => $newOrder->data->postcode,
                        ':county' => $newOrder->data->county,
                        ':country' => $newOrder->data->country,
                        ':phone' => $newOrder->data->telephone,
                        ':sitecode' => $newOrder->data->sitecode,
                        ':siccode' => $newOrder->data->sic_code,
                        ':deldate' => $newOrder->data->actual_delivery_date
                    );

                    $result = $this->rdb->prepare($sql);
                    $result->execute($orderInfo);

                    foreach ($newOrder->items as $item) {
                        $salesOrderInfo = array(
                            ':company' => $newOrder->company->company_id,
                            ':salesOrderId' => $newOrder->sales_order_id,
                            ':salesOrderNumber' => $newOrder->sales_order_number,
                            ':delproddetail' => $item->delivery_product_detail_id,
                            ':productname' => $item->product_name,
                            ':ser' => $item->serial_number,
                            ':qty' => $item->quantity,
                            ':processed' => $item->processed,
                            ':weight' => $item->weight
                        );

                        $sql = 'INSERT INTO recyc_sales_order_detail
							(company_id,sales_order_id,sales_order_number,delivery_product_detail_id,product_name,serial_number,quantity,processed,weight)
							VALUES
							(:company,:salesOrderId,:salesOrderNumber,:delproddetail,:productname,:ser, :qty, :processed, :weight)';

                        $result = $this->rdb->prepare($sql);
                        $result->execute($salesOrderInfo);
                    }

                    $this->rdb->commit();
                    // echo 'added '.$newOrder->sales_order_id.'<br>';
                } catch (\PDOException $ex) {
                    $this->rdb->rollBack();
                    Logger::getInstance("OrderSync.log")->error(
                        'process-error-rollBack',
                        [$ex->getLine(), $e->getMessage()]
                    );
                    echo $ex->getMessage();
                }
            }
        } else {
            Logger::getInstance("OrderSync.log")->info(
                'process-end',
                ["There are no new orders to add."]
            );
            echo "There are no new orders to add.";
            }
        }
    }
}   
