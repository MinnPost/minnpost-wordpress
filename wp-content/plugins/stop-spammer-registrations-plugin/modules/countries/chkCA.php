<?php
// generated Saturday 11th of April 2015 04:11:58 PM
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class chkCA extends be_module {
	public $searchname = 'Canada';
	public $searchlist = array(
			array( '023016000000', '023018000000' ),
			array( '023091128000', '023091160000' ),
			array( '023248000000', '023248160000' ),
			array( '024072000000', '024072144000' ),
			array( '024244064000', '024244080000' ),
			array( '047054000000', '047056000000' ),
			array( '050021224000', '050021240000' ),
			array( '050093000000', '050093128000' ),
			array( '050098000000', '050100000000' ),
			array( '063142160000', '063142176000' ),
			array( '064018160000', '064018192000' ),
			array( '064046000000', '064046064000' ),
			array( '064056224000', '064057000000' ),
			array( '064114000000', '064115000000' ),
			array( '064119208000', '064119224000' ),
			array( '064140112000', '064140128000' ),
			array( '064141000000', '064141128000' ),
			array( '064180000000', '064181000000' ),
			array( '065061192000', '065062000000' ),
			array( '066036128000', '066036160000' ),
			array( '066046000000', '066047000000' ),
			array( '066049128000', '066050000000' ),
			array( '066051096000', '066051128000' ),
			array( '066154096000', '066154128000' ),
			array( '066185192000', '066185224000' ),
			array( '066203192000', '066203224000' ),
			array( '066209176000', '066209192000' ),
			array( '066222128000', '066223000000' ),
			array( '066225160000', '066225192000' ),
			array( '067055000000', '067055064000' ),
			array( '067205064000', '067205128000' ),
			array( '067211064000', '067211080000' ),
			array( '067211112000', '067211128000' ),
			array( '067212064000', '067212096000' ),
			array( '067221144000', '067221160000' ),
			array( '068067032000', '068067064000' ),
			array( '068069128000', '068069160000' ),
			array( '068171064000', '068171080000' ),
			array( '069010128000', '069010160000' ),
			array( '069027096000', '069027128000' ),
			array( '069042048000', '069042064000' ),
			array( '069049032000', '069049064000' ),
			array( '069050128000', '069050192000' ),
			array( '069051192000', '069052000000' ),
			array( '069067160000', '069067192000' ),
			array( '069165128000', '069166000000' ),
			array( '069172064000', '069172128000' ),
			array( '069172144000', '069172192000' ),
			array( '070036048000', '070036064000' ),
			array( '071019240000', '071020000000' ),
			array( '072002000000', '072002064000' ),
			array( '074003128000', '074003192000' ),
			array( '074082064000', '074082096000' ),
			array( '075119224000', '075120000000' ),
			array( '075152000000', '075160000000' ),
			array( '076076096000', '076076128000' ),
			array( '096125128000', '096125144000' ),
			array( '096127192000', '096128000000' ),
			array( '104152168000', '104152172000' ),
			array( '104152208000', '104152212000' ),
			array( '104157000000', '104157128000' ),
			array( '104158000000', '104159000000' ),
			array( '104167096000', '104167128000' ),
			array( '104222112000', '104222128000' ),
			array( '104234000000', '104235000000' ),
			array( '104245004000', '104245008000' ),
			array( '104245144000', '104245148000' ),
			array( '104254088000', '104254096000' ),
			array( '107150224000', '107151000000' ),
			array( '108170128000', '108170192000' ),
			array( '108172000000', '108174000000' ),
			array( '108180000000', '108182000000' ),
			array( '129173000000', '129174000000' ),
			array( '130015000000', '130016000000' ),
			array( '131104000000', '131105000000' ),
			array( '131202000000', '131203000000' ),
			array( '132204000000', '132206000000' ),
			array( '134117000000', '134118000000' ),
			array( '135000000000', '135001000000' ),
			array( '137186000000', '137187000000' ),
			array( '138034000000', '138035000000' ),
			array( '142000016000', '142000032000' ),
			array( '142026000000', '142027000000' ),
			array( '142033000000', '142034000000' ),
			array( '142058000000', '142059000000' ),
			array( '142068000000', '142069000000' ),
			array( '142134000000', '142135000000' ),
			array( '142151000000', '142152000000' ),
			array( '142160000000', '142164000000' ),
			array( '142165000000', '142168000000' ),
			array( '142176000000', '142177000000' ),
			array( '142217000000', '142218000000' ),
			array( '147194000000', '147195000000' ),
			array( '159008038160', '159008038192' ),
			array( '159008135192', '159008135224' ),
			array( '159122086176', '159122086192' ),
			array( '162156000000', '162158000000' ),
			array( '162211184000', '162211188000' ),
			array( '162211224000', '162211232000' ),
			array( '162212100000', '162212104000' ),
			array( '162213156000', '162213160000' ),
			array( '162219176000', '162219180000' ),
			array( '162221200000', '162221208000' ),
			array( '162244024000', '162244032000' ),
			array( '162245144000', '162245148000' ),
			array( '162247012000', '162247016000' ),
			array( '162248008000', '162248012000' ),
			array( '162248160000', '162248168000' ),
			array( '162250188000', '162250192000' ),
			array( '162252240000', '162252244000' ),
			array( '162253128000', '162253132000' ),
			array( '167088032000', '167088048000' ),
			array( '167088128000', '167088144000' ),
			array( '168144000000', '168145000000' ),
			array( '168235144000', '168235160000' ),
			array( '170075160000', '170075176000' ),
			array( '172218000000', '172220000000' ),
			array( '173180000000', '173184000000' ),
			array( '173206000000', '173207000000' ),
			array( '173209112000', '173209128000' ),
			array( '173210128000', '173211000000' ),
			array( '173224240000', '173225000000' ),
			array( '173243032000', '173243048000' ),
			array( '173246000000', '173246032000' ),
			array( '174035128000', '174036000000' ),
			array( '184075208000', '184075224000' ),
			array( '184094000000', '184094128000' ),
			array( '184175000000', '184175064000' ),
			array( '185014193000', '185014194000' ),
			array( '192064008000', '192064016000' ),
			array( '192064040000', '192064044000' ),
			array( '192067222000', '192067223000' ),
			array( '192095128000', '192096000000' ),
			array( '192099000000', '192100000000' ),
			array( '192139153000', '192139154000' ),
			array( '192159192000', '192160000000' ),
			array( '192197128000', '192197129000' ),
			array( '192199048000', '192199064000' ),
			array( '192206004000', '192206008000' ),
			array( '192222128000', '192223000000' ),
			array( '192249096000', '192249112000' ),
			array( '192252160000', '192252176000' ),
			array( '193183105000', '193183105128' ),
			array( '198027064000', '198027128000' ),
			array( '198051075000', '198051076000' ),
			array( '198072096000', '198072128000' ),
			array( '198073050000', '198073051000' ),
			array( '198096155000', '198096156000' ),
			array( '198100144000', '198100160000' ),
			array( '198144144000', '198144160000' ),
			array( '198245048000', '198245064000' ),
			array( '198254128000', '198255000000' ),
			array( '199016128000', '199016132000' ),
			array( '199019092000', '199019096000' ),
			array( '199019212000', '199019216000' ),
			array( '199021148000', '199021152000' ),
			array( '199058080000', '199058084000' ),
			array( '199058232000', '199058240000' ),
			array( '199087152000', '199087160000' ),
			array( '199091116000', '199091120000' ),
			array( '199096088000', '199096096000' ),
			array( '199101056000', '199101064000' ),
			array( '199119232000', '199119236000' ),
			array( '199167136000', '199167140000' ),
			array( '199185032000', '199185064000' ),
			array( '199201120000', '199201128000' ),
			array( '199204044000', '199204048000' ),
			array( '199229220000', '199229224000' ),
			array( '204014072000', '204014080000' ),
			array( '204187100000', '204187102000' ),
			array( '204191000000', '204192000000' ),
			array( '204197176000', '204197192000' ),
			array( '204237000000', '204237128000' ),
			array( '205151162000', '205151164000' ),
			array( '205189071000', '205189072000' ),
			array( '205200000000', '205201000000' ),
			array( '205204064000', '205204096000' ),
			array( '205207236000', '205207237000' ),
			array( '205211032000', '205211064000' ),
			array( '205250000000', '205251000000' ),
			array( '206045000000', '206046000000' ),
			array( '206075000000', '206076000000' ),
			array( '206116000000', '206117000000' ),
			array( '206248128000', '206248192000' ),
			array( '207006000000', '207007000000' ),
			array( '207102000000', '207103000000' ),
			array( '207134000000', '207135000000' ),
			array( '207161000000', '207162000000' ),
			array( '207194000000', '207195000000' ),
			array( '207216000000', '207217000000' ),
			array( '207230224000', '207231000000' ),
			array( '207245192000', '207246000000' ),
			array( '208038000000', '208038064000' ),
			array( '208066016000', '208066020000' ),
			array( '208076104000', '208076112000' ),
			array( '208077156000', '208077160000' ),
			array( '208079216000', '208079220000' ),
			array( '208088004000', '208088008000' ),
			array( '208089128000', '208089132000' ),
			array( '208101064000', '208101128000' ),
			array( '208111064000', '208111096000' ),
			array( '208114128000', '208114192000' ),
			array( '209044096000', '209044128000' ),
			array( '209082000000', '209082128000' ),
			array( '209089000000', '209090000000' ),
			array( '209090240000', '209091000000' ),
			array( '209091064000', '209091128000' ),
			array( '209097192000', '209097224000' ),
			array( '209112000000', '209112064000' ),
			array( '209121000000', '209122000000' ),
			array( '209141128000', '209141208000' ),
			array( '209161192000', '209162000000' ),
			array( '209171000000', '209172000000' ),
			array( '209183000000', '209183032000' ),
			array( '209222048000', '209222064000' ),
			array( '209239000000', '209239032000' ),
			array( '216046000000', '216046032000' ),
			array( '216108000000', '216108128000' ),
			array( '216110224000', '216111000000' ),
			array( '216129064000', '216129096000' ),
			array( '216145096000', '216145112000' ),
			array( '216154000000', '216154128000' ),
			array( '216183128000', '216183160000' ),
			array( '216191000000', '216192000000' ),
			array( '216218000000', '216218064000' ),
			array( '216232000000', '216233000000' )
		);
}

?>