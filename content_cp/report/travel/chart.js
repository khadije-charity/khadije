function chartDrawer()
{
  if($("#chartdiv").length == 1){new_chart();}
}



function highChart()
{

Highcharts.chart('chartdiv',
{
  chart: {
    zoomType: 'x',
    style: {
      fontFamily: 'IRANSans, Tahoma, sans-serif'
    }
  },
  title: {
    text: ''
  },
  xAxis: [{
    crosshair: true
  }],
  yAxis: [{ // Primary yAxis
    labels: {
      format: '{value}',
      style: {
        color: Highcharts.getOptions().colors[0]
      }
    },
    title: {
      text: '{%trans "Sum price"%}',
      useHTML: Highcharts.hasBidiBug,
      style: {
        color: Highcharts.getOptions().colors[0]
      }
    }
  }],
  tooltip: {
    useHTML: true,
    borderWidth: 0,
    shared: true
  },
  exporting:
  {
    buttons:
    {
      contextButton:
      {
        menuItems:
        [
         'printChart',
         'separator',
         'downloadPNG',
         'downloadJPEG',
         'downloadSVG'
        ]
      }
    }
  },
  credits:
  {
      text: '{{service.title}}',
      href: '{{service.url}}',
      position:
      {
          x: -35,
          y: -7
      },
      style: {
          fontWeight: 'bold'
      }
  },
  legend: {
    layout: 'vertical',
    align: 'left',
    x: 120,
    verticalAlign: 'top',
    y: 100,
    floating: true,
    backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || 'rgba(255,255,255,0.25)'
  },
  series: [
  {
    name: '{%trans "Sum price"%}',
    type: 'column',
    data: {{chartTable.value | raw}},
    tooltip: {
      valueSuffix: ' {%trans "Toman"%}'
    }

  }
  ]
}, function(_chart)
  {
    _chart.renderer.image('{{service.logo}}', 10, 5, 30, 30).attr({class: 'chartServiceLogo'}).add();
  });
}


function new_chart()
{
  Highcharts.chart('chartdiv', {

  title: {
    text: 'Highcharts Sankey Diagram'
  },

  series: [{
    keys: ['from', 'to', 'weight'],
    data: {{chartTable.value | raw}},
    type: 'sankey',
    name: 'Sankey demo series'
  }]

}, function(_chart)
  {
    _chart.renderer.image('{{service.logo}}', 10, 5, 30, 30).attr({class: 'chartServiceLogo'}).add();
});


}