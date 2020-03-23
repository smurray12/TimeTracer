<?php 
  require('dbinfo.inc');
  $title = "Summary Report";
?>

<!DOCTYPE HTML>
<html>
<head> 
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link href="CSS/project.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>
  <title><?php $title ?></title>
</head>
<body>

  <!-- Top banner -->
  <header>
    <div class="headContainer">
      <h1 class="logo">Time Tracer</h1>

      <nav>
        <ul><li><a href="#">Log out</a></li></ul>
      </nav>

    </div>
  </header>

  <div id="wrapper" >
  <!-- Home nav -->
  <div id="main_body" class="image"> 
    <div class="dropdown">
      <button onclick="document.location = 'page1.html'" class="dropbtn">Home</button>
    </div>
        
      <div class="dropdown">
          <button class="dropbtn">Courses</button>
            <div class="dropdown-content">
            <a href="./course311.html">CSCI 311</a>
            <a href="./course331.html">CSCI 331</a>
            <a href="./course375.html">CSCI 375</a>
            </div>
      </div>
      <div class="dropdown">
          <button class="dropbtn" onclick="document.location = 'graph.html'">Reports</button>
      </div>

    <!-- Graphs start -->
     <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-2 font-weight-bold text-white">Summary Report</h1>
           <p class="mb-4 text-white">Here is your summary report of all your classes and tasks.</p>

           <!-- Content Row -->
          <div class="row">

            <div class="col-xl-8 col-lg-7">

              <!-- Bar Chart (Total Hours per course) -->
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold dark">Total Hours Per Course</h6>
                </div>
                <div class="card-body">
                  <div class="chart-area">
                    <canvas id="myChart"></canvas>
                  </div>
                   <hr>
                  This chart represents your total hours tracked per course. This includes all hours with and without a description.
                </div>
              </div>

               <!-- Pie Chart -->
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold dark">Total Hours Per Task (Description)</h6>
                </div>
                <div class="card-body">
                  <div class="chart-bar">
                    <canvas id="pie"></canvas>
                  </div>
                  <hr>
                  This chart represents your total hours tracked per task (description). 
                </div>
              </div>
            </div>

            <!-- Gauge Chart - Total Study Hours / Procrastination Hours-->
            <div class="col-xl-4 col-lg-5">
              <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold dark">Study vs Procrastination</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                  <div class="chart-pie pt-4">
                    <canvas id="gauge"></canvas>
                  </div>
                  <hr>
                  This chart represents your total study hours vs all the time you missed out on studying!
                </div>
              </div>
            </div>
          </div>
      </div> <!--container fluid -->
  </div>

            <!-- Days you studied the most, at what time? -->

<?php 
  require('dbinfo.inc');

    try {
      $dbh = new PDO("mysql:host=$host; dbname=$database", $user, $password);
      $stmt = $dbh->prepare("SELECT * FROM Courses");
      $stmt->execute();

      $courseLabels = [];
      $courseTime = [];
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $courseLabels[] = $course_id;
        $courseTime[] = (double)$duration;
       // echo $course_id;
       // echo $description;
       // echo $time;

      }
      // Get total time spent based on description
      $qry = $dbh->prepare("SELECT description, sec_to_time(SUM(time_to_sec(duration))) timetotal
                            FROM courses
                            GROUP BY description");
      $qry->execute();

      $taskType = [];
      $totalTime = [];
      while($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $taskType[] = $description;
        $totalTime[] = (double)$timetotal; 
        // echo $description; 
         echo $timetotal;
      }

    // echo json_encode($taskType);
    // echo json_encode($totalTime);


    } catch(PDOException $e) {
      print "Error!" . $e->getMessage()."<br/>"; 
    }

   
?>

  <script>

    let myChart = document.getElementById('myChart').getContext('2d');

    // Global Options
    Chart.defaults.global.defaultFontFamily = 'Lato';
    Chart.defaults.global.defaultFontSize = 18;
    Chart.defaults.global.defaultFontColor = '#777';

    // Bar Chart - Total Hours Per Course
    let courseHours = new Chart(myChart, {
      type:'bar', // bar, horizontalBar, pie, line, doughnut, radar, polarArea
      data:{
        labels: <?php echo json_encode($courseLabels); ?>,
        datasets:[{
          label:'Hours',
          data: <?php print_r(json_encode($courseTime)); ?>,
         
          backgroundColor:[
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)',
            'rgba(153, 102, 255, 0.6)',
            'rgba(255, 159, 64, 0.6)',
            'rgba(255, 99, 132, 0.6)'
          ],
          borderWidth:1,
          borderColor:'#777',
          hoverBorderWidth:3,
          hoverBorderColor:'#000'
        }]
      },
      options:{
        title:{
          display:true,
          fontSize:25
        },
        legend:{
          display:true,
          position:'right',
          labels:{
            fontColor:'#000'
          }
        },
        layout:{
          padding:{
            left:50,
            right:0,
            bottom:0,
            top:0
          }
        },
        tooltips:{
          enabled:true
        }
      }
    });

    // Pie Chart - Total Hours Per task (description) 
    let pie = document.getElementById('pie').getContext('2d');
    let taskChart = new Chart(pie, {
      type:'pie', // bar, horizontalBar, pie, line, doughnut, radar, polarArea
      data:{
        labels: <?php echo json_encode($taskType); ?>,
        datasets:[{
          label:'Hours',
          data: <?php echo json_encode($totalTime); ?>,
         
          backgroundColor:[
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)',
            'rgba(153, 102, 255, 0.6)',
            'rgba(255, 159, 64, 0.6)',
            'rgba(255, 99, 132, 0.6)'
          ],
          borderWidth:1,
          borderColor:'#777',
          hoverBorderWidth:3,
          hoverBorderColor:'#000'
        }]
      },
      options:{
        title:{
          display:true,
          fontSize:25
        },
        legend:{
          display:true,
          position:'right',
          labels:{
            fontColor:'#000'
          }
        },
        layout:{
          padding:{
            left:50,
            right:0,
            bottom:0,
            top:0
          }
        },
        tooltips:{
          enabled:true
        }
      }
    });

    // Gauge Chart - Total Study Hours / Procrastination Hours
    var ctx = document.getElementById('gauge').getContext('2d');
    var gauge = new Chart(ctx, {
        // The type of chart we want to create
        type: 'doughnut',
      data:{
        labels:['Study Hours', 'Procrastination Hours'],
        datasets:[{
          label:'Hours',
          data:[
            34,
            94
          ],
         
          backgroundColor:[
            'rgba(103, 230, 220,1.0)',
            'rgba(255, 99, 50, 0.6)'
          ],
          borderWidth:1,
          borderColor:'#777',
          hoverBorderWidth:3,
          hoverBorderColor:'#000'
        }]
      },
      options:{
        title:{
          display:true,
          fontSize:25
        },
        legend:{
          display:true,
          labels:{
            fontColor:'#000'
          }
        },
        layout:{
          padding:{
            left:50,
            right:0,
            bottom:0,
            top:0
          }
        },
        tooltips:{
          enabled:true
        },
          circumference: 1 * Math.PI,
          rotation: 1 * Math.PI,
          cutoutPercentage: 90
      }
    });
</script>

        <footer>&copy; Copyright Feb 2020 SCUBE </footer>
        
</body>
</html>
