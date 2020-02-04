<?php
// generated Saturday 11th of April 2015 04:13:22 PM
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class chkTR extends be_module {
	public $searchname = 'Turkey';
	public $searchlist = array(
			array( '005008000000', '005016000000' ),
			array( '005025128000', '005025192000' ),
			array( '005027128000', '005028000000' ),
			array( '005046000000', '005048000000' ),
			array( '005063032000', '005063064000' ),
			array( '005250245000', '005250246000' ),
			array( '005255192000', '006000000000' ),
			array( '024133120000', '024133128000' ),
			array( '024133200000', '024133208000' ),
			array( '024133216000', '024133224000' ),
			array( '031007038000', '031007039000' ),
			array( '031044199000', '031044200000' ),
			array( '031141128000', '031142000000' ),
			array( '031142128000', '031142192000' ),
			array( '031145039000', '031145040000' ),
			array( '031155000000', '031155032000' ),
			array( '031169080000', '031169081000' ),
			array( '031192208000', '031192216000' ),
			array( '031200008000', '031200024000' ),
			array( '031210039000', '031210040000' ),
			array( '031210047000', '031210048000' ),
			array( '031210063000', '031210064000' ),
			array( '031210100000', '031210101000' ),
			array( '031210113000', '031210114000' ),
			array( '031210122000', '031210123000' ),
			array( '031210127000', '031210128000' ),
			array( '031223000000', '031223128000' ),
			array( '037075010000', '037075011000' ),
			array( '037077012000', '037077015000' ),
			array( '037077016000', '037077020000' ),
			array( '037123000000', '037123064000' ),
			array( '037123096000', '037123100000' ),
			array( '037154000000', '037156000000' ),
			array( '037230104000', '037230112000' ),
			array( '037247100000', '037247102000' ),
			array( '037247103000', '037247104000' ),
			array( '046001000000', '046002024000' ),
			array( '046002040000', '046002044000' ),
			array( '046045160000', '046045161000' ),
			array( '046045166000', '046045167000' ),
			array( '046045168000', '046045170000' ),
			array( '046045171000', '046045173000' ),
			array( '046045176000', '046045184000' ),
			array( '046196000000', '046198000000' ),
			array( '046252096000', '046252112000' ),
			array( '062029032000', '062029048000' ),
			array( '062248000000', '062248128000' ),
			array( '077223128000', '077223144000' ),
			array( '077223154000', '077223155000' ),
			array( '078135000000', '078135004000' ),
			array( '078135016000', '078135020000' ),
			array( '078135028000', '078135040000' ),
			array( '078135097000', '078135098000' ),
			array( '078135101000', '078135102000' ),
			array( '078135111000', '078135112000' ),
			array( '078160000000', '078164000000' ),
			array( '078164128000', '078165064000' ),
			array( '078165128000', '078165192000' ),
			array( '078166000000', '078168192000' ),
			array( '078169000000', '078171000000' ),
			array( '078171064000', '078172128000' ),
			array( '078172192000', '078175064000' ),
			array( '078175128000', '078186128000' ),
			array( '078187000000', '078192000000' ),
			array( '079123128000', '079124000000' ),
			array( '080093215000', '080093216000' ),
			array( '081213000000', '081216000000' ),
			array( '082145224000', '082146000000' ),
			array( '082151128000', '082151160000' ),
			array( '082222040000', '082222044000' ),
			array( '082222048000', '082222049000' ),
			array( '082222100000', '082222112000' ),
			array( '082222120000', '082222128000' ),
			array( '082222208000', '082222224000' ),
			array( '082222232000', '082222240000' ),
			array( '082222247000', '082222248000' ),
			array( '084051037000', '084051038000' ),
			array( '085029051000', '085029052000' ),
			array( '085095253000', '085095254000' ),
			array( '085096000000', '085111128000' ),
			array( '085159068000', '085159069000' ),
			array( '086108192000', '086108208000' ),
			array( '088205096000', '088205128000' ),
			array( '088224000000', '088237000000' ),
			array( '088238000000', '088239000000' ),
			array( '088240000000', '088248064000' ),
			array( '088248080000', '089000000000' ),
			array( '089019015000', '089019016000' ),
			array( '089019026000', '089019027000' ),
			array( '090158064000', '090158128000' ),
			array( '090159000000', '090160000000' ),
			array( '091093007000', '091093008000' ),
			array( '091093019000', '091093020000' ),
			array( '091093030000', '091093031000' ),
			array( '092044061000', '092044062000' ),
			array( '092044114000', '092044115000' ),
			array( '092044164000', '092044168000' ),
			array( '092044170000', '092044171000' ),
			array( '092045030000', '092045031000' ),
			array( '092045128000', '092045192000' ),
			array( '092045200000', '092045208000' ),
			array( '092045250000', '092045252000' ),
			array( '092063000000', '092063016000' ),
			array( '093187200000', '093187208000' ),
			array( '094048000000', '094056000000' ),
			array( '094073137000', '094073138000' ),
			array( '094073156000', '094073157000' ),
			array( '094078064000', '094078128000' ),
			array( '094079077000', '094079078000' ),
			array( '094102000000', '094102016000' ),
			array( '094120000000', '094122128000' ),
			array( '094122160000', '094122192000' ),
			array( '094123064000', '094123128000' ),
			array( '094123192000', '094123208000' ),
			array( '094138216000', '094138220000' ),
			array( '094199200000', '094199208000' ),
			array( '095000000000', '095001000000' ),
			array( '095005000000', '095006128000' ),
			array( '095007000000', '095011000000' ),
			array( '095013000000', '095013128000' ),
			array( '095014000000', '095014128000' ),
			array( '095015000000', '095016000000' ),
			array( '095070000000', '095071000000' ),
			array( '095142132000', '095142136000' ),
			array( '095173160000', '095173192000' ),
			array( '095173224000', '095174000000' ),
			array( '095183128000', '095184000000' ),
			array( '109228192000', '109229000000' ),
			array( '139179064000', '139179096000' ),
			array( '144122112000', '144122128000' ),
			array( '159146000000', '159146128000' ),
			array( '159253032000', '159253048000' ),
			array( '176033104000', '176033112000' ),
			array( '176033128000', '176033136000' ),
			array( '176033138000', '176033139000' ),
			array( '176033192000', '176033224000' ),
			array( '176040011000', '176040012000' ),
			array( '176040016000', '176040024000' ),
			array( '176040040000', '176040048000' ),
			array( '176040050000', '176040052000' ),
			array( '176040064000', '176040072000' ),
			array( '176040088000', '176040096000' ),
			array( '176040128000', '176040136000' ),
			array( '176040146000', '176040147000' ),
			array( '176040148000', '176040149000' ),
			array( '176040150000', '176040151000' ),
			array( '176040160000', '176040196000' ),
			array( '176040198000', '176040200000' ),
			array( '176040208000', '176041000000' ),
			array( '176041064000', '176041096000' ),
			array( '176041120000', '176041132000' ),
			array( '176041136000', '176041148000' ),
			array( '176041168000', '176041178000' ),
			array( '176041184000', '176041192000' ),
			array( '176041208000', '176041224000' ),
			array( '176041228000', '176041232000' ),
			array( '176041248000', '176042000000' ),
			array( '176042056000', '176042064000' ),
			array( '176042076000', '176042088000' ),
			array( '176042104000', '176042112000' ),
			array( '176042136000', '176042144000' ),
			array( '176042160000', '176042168000' ),
			array( '176042208000', '176042216000' ),
			array( '176042224000', '176042232000' ),
			array( '176043000000', '176043001000' ),
			array( '176043014000', '176043016000' ),
			array( '176043024000', '176043032000' ),
			array( '176043104000', '176043128000' ),
			array( '176043140000', '176043141000' ),
			array( '176043142000', '176043143000' ),
			array( '176043145000', '176043146000' ),
			array( '176043172000', '176043174000' ),
			array( '176043192000', '176043200000' ),
			array( '176043216000', '176043248000' ),
			array( '176053011000', '176053014000' ),
			array( '176053024000', '176053025000' ),
			array( '176053063000', '176053064000' ),
			array( '176053069000', '176053070000' ),
			array( '176053122000', '176053123000' ),
			array( '176055000000', '176056000000' ),
			array( '176219128000', '176219176000' ),
			array( '178020224000', '178020232000' ),
			array( '178211033000', '178211034000' ),
			array( '178211043000', '178211045000' ),
			array( '178211053000', '178211054000' ),
			array( '178211055000', '178211056000' ),
			array( '178233000000', '178233128000' ),
			array( '178240000000', '178240064000' ),
			array( '178240128000', '178240192000' ),
			array( '178241000000', '178241032000' ),
			array( '178241064000', '178241160000' ),
			array( '178243192000', '178243224000' ),
			array( '178243240000', '178244000000' ),
			array( '178245128000', '178246000000' ),
			array( '178247000000', '178247128000' ),
			array( '178250090000', '178250091000' ),
			array( '185004224000', '185004228000' ),
			array( '185007176000', '185007177000' ),
			array( '185008012000', '185008016000' ),
			array( '185012108000', '185012112000' ),
			array( '185015040000', '185015044000' ),
			array( '185019082000', '185019083000' ),
			array( '185022187000', '185022188000' ),
			array( '185024124000', '185024128000' ),
			array( '185044192000', '185044193000' ),
			array( '185051036000', '185051040000' ),
			array( '185051112000', '185051113000' ),
			array( '185059028000', '185059029000' ),
			array( '185065206000', '185065207000' ),
			array( '188056040000', '188056048000' ),
			array( '188056128000', '188057064000' ),
			array( '188057128000', '188058064000' ),
			array( '188058128000', '188058192000' ),
			array( '188119000000', '188119064000' ),
			array( '188124028000', '188124029000' ),
			array( '188124031000', '188124032000' ),
			array( '188132135000', '188132136000' ),
			array( '188132204000', '188132205000' ),
			array( '188132210000', '188132211000' ),
			array( '188132212000', '188132213000' ),
			array( '188132226000', '188132227000' ),
			array( '188164064000', '188164096000' ),
			array( '193104130000', '193104131000' ),
			array( '193110213000', '193110214000' ),
			array( '193140000000', '193140240000' ),
			array( '193192096000', '193192128000' ),
			array( '193255000000', '194000000000' ),
			array( '194027000000', '194028000000' ),
			array( '194054032000', '194054064000' ),
			array( '195087128000', '195088000000' ),
			array( '195112128000', '195112160000' ),
			array( '195142040000', '195142044000' ),
			array( '195142122000', '195142123000' ),
			array( '195142163000', '195142164000' ),
			array( '195142217000', '195142218000' ),
			array( '195155164000', '195155168000' ),
			array( '195155192000', '195156000000' ),
			array( '195174000000', '195175128000' ),
			array( '195175192000', '195176000000' ),
			array( '195244034000', '195244035000' ),
			array( '195244036000', '195244037000' ),
			array( '212002216000', '212002224000' ),
			array( '212015016000', '212015032000' ),
			array( '212057015000', '212057016000' ),
			array( '212058002000', '212058003000' ),
			array( '212058008000', '212058010000' ),
			array( '212068034000', '212068035000' ),
			array( '212068044000', '212068045000' ),
			array( '212068051000', '212068052000' ),
			array( '212098192000', '212098224000' ),
			array( '212156000000', '212156096000' ),
			array( '212156124000', '212156224000' ),
			array( '212174000000', '212176000000' ),
			array( '212252018000', '212252019000' ),
			array( '212252021000', '212252022000' ),
			array( '212252023000', '212252024000' ),
			array( '212252056000', '212252058000' ),
			array( '212252096000', '212252104000' ),
			array( '212252132000', '212252133000' ),
			array( '212252136000', '212252160000' ),
			array( '212252164000', '212252165000' ),
			array( '212252167000', '212252168000' ),
			array( '212252193000', '212252194000' ),
			array( '212253032000', '212253064000' ),
			array( '212253072000', '212253076000' ),
			array( '212253088000', '212253092000' ),
			array( '212253095000', '212253096000' ),
			array( '212253107000', '212253108000' ),
			array( '212253124000', '212253126000' ),
			array( '212253144000', '212253192000' ),
			array( '212253224000', '212254000000' ),
			array( '213014113000', '213014114000' ),
			array( '213014123000', '213014124000' ),
			array( '213014128000', '213014145000' ),
			array( '213043000000', '213043064000' ),
			array( '213043096000', '213043160000' ),
			array( '213043176000', '213043184000' ),
			array( '213043242000', '213043243000' ),
			array( '213074152000', '213074160000' ),
			array( '213074208000', '213074210000' ),
			array( '213074224000', '213074240000' ),
			array( '213074254000', '213074255000' ),
			array( '213128077000', '213128078000' ),
			array( '213153157000', '213153158000' ),
			array( '213153160000', '213153164000' ),
			array( '213153197000', '213153198000' ),
			array( '213153204000', '213153205000' ),
			array( '213232000000', '213232032000' ),
			array( '213238128000', '213238129000' ),
			array( '213238167000', '213238168000' ),
			array( '213238178000', '213238179000' ),
			array( '217131002000', '217131003000' ),
			array( '217131028000', '217131029000' ),
			array( '217131064000', '217131096000' ),
			array( '217131192000', '217132000000' ),
			array( '217195202000', '217195203000' )
		);
}

?>