<?php 
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

return array(
      'all_business_type' => array(
            ''                              => 'Select Business Type (Optional)', 
            'animalshelter'                 => 'Animal Shelter',
            'automotivebusiness'            => 'Automotive Business',
            'childcare'                     => 'ChildCare',
            'dentist'                       => 'Dentist',
            'drycleaningorlaundry'          => 'Dry Cleaning Or Laundry',
            'emergencyservice'              => 'Emergency Service',
            'employmentagency'              => 'Employment Agency',
            'entertainmentbusiness'         => 'Entertainment Business',
            'financialservice'              => 'Financial Service',
            'foodestablishment'             => 'Food Establishment',
            'governmentoffice'              => 'Government Office',
            'healthandbeautybusiness'       => 'Health And Beauty Business',
            'homeandconstructionbusiness'   => 'Home And Construction Business',
            'internetcafe'                  => 'Internet Cafe',
            'legalservice'                  => 'Legal Service',
            'library'                       => 'Library',
            'lodgingbusiness'               => 'Lodging Business',
            'medicalbusiness'               => 'Medical Business',
            'professionalservice'           => 'Professional Service',
            'radiostation'                  => 'Radio Station',
            'realestateagent'               => 'Real Estate Agent',
            'recyclingcenter'               => 'Recycling Center',
            'selfstorage'                   => 'Self Storage',
            'shoppingcenter'                => 'Shopping Center',
            'sportsactivitylocation'        => 'Sports Activity Location',
            'store'                         => 'Store',
            'televisionstation'             => 'Television Station',
            'touristinformationcenter'      => 'Tourist Information Center',
            'travelagency'                  => 'Travel Agency',
      ),      
      'automotivebusiness' => array(
          ''                  => 'Select Sub Business Type ( optional )',  
          'autobodyshop'      => 'Auto Body Shop',
          'autodealer'        => 'Auto Dealer',
          'autopartsstore'    => 'Auto Parts Store',
          'autorental'        => 'Auto Rental',
          'autorepair'        => 'Auto Repair',
          'autowash'          => 'Auto Wash',
          'gasstation'        => 'Gas Station',
          'motorcycledealer'  => 'Motorcycle Dealer',
          'motorcyclerepair'  => 'Motorcycle Repair'
      ), 
        'emergencyservice' => array(
          ''               => 'Select Sub Business Type ( optional )',     
          'firestation'    => 'Fire Station',
          'hospital'       => 'Hospital',
          'policestation'  => 'Police Station',                                    
      ), 
        'entertainmentbusiness' => array(
           ''                   => 'Select Sub Business Type ( optional )',  
           'adultentertainment' => 'Adult Entertainment',
           'amusementpark'      => 'Amusement Park',
           'artgallery'         => 'Art Gallery',
           'casino'             => 'Casino',
           'comedyclub'         => 'Comedy Club',
           'movietheater'       => 'Movie Theater',
           'nightclub'          => 'Night Club',

      ),                                                      
        'financialservice' => array(
           ''                   => 'Select Sub Business Type ( optional )',   
           'accountingservice'  => 'Accounting Service',
           'automatedteller'    => 'Automated Teller',
           'bankorcredit_union' => 'Bank Or Credit Union',
           'insuranceagency'    => 'Insurance Agency',                                      

      ),   
        'foodestablishment' => array(
           ''                   => 'Select Sub Business Type ( optional )',    
           'bakery'             => 'Bakery',
           'barorpub'           => 'Bar Or Pub',
           'brewery'            => 'Brewery',
           'cafeorcoffee_shop'  => 'Cafe Or Coffee Shop', 
           'fastfoodrestaurant' => 'Fast Food Restaurant',
           'icecreamshop'       => 'Ice Cream Shop',
           'restaurant'         => 'Restaurant',
           'winery'             => 'Winery', 

      ),
        'healthandbeautybusiness' => array(
           ''             => 'Select Sub Business Type ( optional )',    
           'beautysalon'  => 'Beauty Salon',
           'dayspa'       => 'DaySpa',
           'hairsalon'    => 'Hair Salon',
           'healthclub'   => 'Health Club', 
           'nailsalon'    => 'Nail Salon',
           'tattooparlor' => 'Tattoo Parlor',                                                                          
      ),   
        'homeandconstructionbusiness' => array(
           ''                  => 'Select Sub Business Type ( optional )',  
           'electrician'       => 'Electrician',
           'generalcontractor' => 'General Contractor',
           'hvacbusiness'      => 'HVAC Business',
           'locksmith'         => 'Locksmith', 
           'movingcompany'     => 'Moving Company',
           'plumber'           => 'Plumber',       
           'roofingcontractor' => 'Roofing Contractor',
           'housepainter'      => 'House Painter',   
      ),   
        'legalservice' => array(
           ''         => 'Select Sub Business Type ( optional )',  
           'attorney' => 'Attorney',
           'notary'   => 'Notary',                                            
      ),  
        'lodgingbusiness' => array(
           ''                => 'Select Sub Business Type ( optional )',  
           'bedandbreakfast' => 'Bed And Breakfast',
           'campground'      => 'Campground',
           'hostel'          => 'Hostel',
           'hotel'           => 'Hotel',
           'motel'           => 'Motel',
           'resort'          => 'Resort',
      ),   
        'sportsactivitylocation' => array(
           ''                    => 'Select Sub Business Type ( optional )',  
           'bowlingalley'        => 'Bowling Alley',
           'exercisegym'         => 'Exercise Gym',
           'golfcourse'          => 'Golf Course',
           'healthclub'          => 'Health Club',
           'publicswimming_pool' => 'Public Swimming Pool',
           'skiresort'           => 'Ski Resort',
           'sportsclub'          => 'Sports Club',
           'stadiumorarena'      => 'Stadium Or Arena',
           'tenniscomplex'       => 'Tennis Complex'
      ),  
        'store' => array(
             ''                      => 'Select Sub Business Type ( optional )',  
             'autopartsstore'        => 'Auto Parts Store',
             'bikestore'             => 'Bike Store',
             'bookstore'             => 'Book Store',
             'clothingstore'         => 'Clothing Store',
             'computerstore'         => 'Computer Store',
             'conveniencestore'      => 'Convenience Store',
             'departmentstore'       => 'Department Store',
             'electronicsstore'      => 'Electronics Store',
             'florist'               => 'Florist',
             'furniturestore'        => 'Furniture Store',
             'gardenstore'           => 'Garden Store',
             'grocerystore'          => 'Grocery Store',
             'hardwarestore'         => 'Hardware Store',
             'hobbyshop'             => 'Hobby Shop',
             'homegoodsstore'        => 'HomeGoods Store',
             'jewelrystore'          => 'Jewelry Store',
             'liquorstore'           => 'Liquor Store',
             'mensclothingstore'     => 'Mens Clothing Store',
             'mobilephonestore'      => 'Mobile Phone Store',
             'movierentalstore'      => 'Movie Rental Store',
             'musicstore'            => 'Music Store',
             'officeequipmentstore'  => 'Office Equipment Store',
             'outletstore'           => 'Outlet Store',
             'pawnshop'              => 'Pawn Shop',
             'petstore'              => 'Pet Store',
             'shoestore'             => 'Shoe Store',
             'sportinggoodsstore'    => 'Sporting Goods Store',
             'tireshop'              => 'Tire Shop',
             'toystore'              => 'Toy Store',
             'wholesalestore'        => 'Wholesale Store'
      ),
        'medicalbusiness' => array(
                             ''                 => 'Select Sub Business Type ( optional )',  
                             'Communityhealth'  => 'Community Health',
                             'dentist'          => 'Dentist',
                             'dermatology'      => 'Dermatology',
                             'dietnutrition'    => 'Diet Nutrition',
                             'emergency'        => 'Emergency',
                             'geriatric'        => 'Geriatric',
                             'gynecologic'      => 'Gynecologic',
                             'medicalclinic'    => 'Medical Clinic',
                             'midwifery'        => 'Midwifery',                              
                              'nursing'         => 'Nursing',
                              'obstetric'       => 'Obstetric',
                              'oncologic'       => 'Oncologic',
                              'optician'        => 'Optician',
                              'optometric'      => 'Optometric',
                              'otolaryngologic' => 'Otolaryngologic',
                              'pediatric'       => 'Pediatric',
                              'pharmacy'        => 'Pharmacy',
                              'physician'       => 'Physician',
                              'physiotherapy'   => 'Physiotherapy',
                              'plasticsurgery'  => 'Plastic Surgery',
                              'podiatric'       => 'Podiatric',
                              'primarycare'     => 'Primary Care',
                              'psychiatric'     => 'Psychiatric',
                              'publichealth'    => 'Public Health',
                )
 );                             