
var profiles =
{

	window800:
	{
		height:800,
		width:800,
		status:1
	},

	window200:
	{
		height:200,
		width:200,
		status:1,
		resizable:0
	},

	windowCenter:
	{
		height:300,
		width:400,
		center:1
	},
	
	windowNotNew:
	{
		height:300,
		width:400,
		center:1,
		createnew:0
	}

};


jQuery(function()
{
	jQuery(".popupwindow").popupwindow(profiles);
});

