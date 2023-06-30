<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="/css/bundy_homepage.css" rel="stylesheet" type="text/css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <title>Bundy Homepage</title>
  </head>
  <body>
    <!-- NAVIGATION BAR -->
    <div class="navbar">
      <a href="/help">
        <p>What is the <b class="p1">LVCC</b><b class="p2"> Bundy</b></p>
      </a>

      <a href="{{ route('login') }}" ><button>Login</button></a>
    </div>

    <!-- CLOCK and CALENDAR -->
    <div class="date_time_Container">
      <!-- CLOCK -->
      <div class="clock_container">
        <div class="time">
          <span class="hms"></span>
          <span class="ampm"></span>
          <br />
          <span class="date"></span>
        </div>
      </div>
      <script>
        function updateTime() {
          var dateInfo = new Date();

          /* time */
          var hr,
            _min =
              dateInfo.getMinutes() < 10
                ? "0" + dateInfo.getMinutes()
                : dateInfo.getMinutes(),
            sec =
              dateInfo.getSeconds() < 10
                ? "0" + dateInfo.getSeconds()
                : dateInfo.getSeconds(),
            ampm = dateInfo.getHours() >= 12 ? "PM" : "AM";

          // replace 0 with 12 at midnight, subtract 12 from hour if 13–23
          if (dateInfo.getHours() == 0) {
            hr = 12;
          } else if (dateInfo.getHours() > 12) {
            hr = dateInfo.getHours() - 12;
          } else {
            hr = dateInfo.getHours();
          }
          // Add to time format

          var currentTime = hr + ":" + _min + ":" + sec;

          // AM/ PM options

          document.getElementsByClassName("hms")[0].innerHTML = currentTime;
          document.getElementsByClassName("ampm")[0].innerHTML = ampm;

          /* date */
          var dow = [
              "Sunday",
              "Monday",
              "Tuesday",
              "Wednesday",
              "Thursday",
              "Friday",
              "Saturday",
            ],
            month = [
              "January",
              "February",
              "March",
              "April",
              "May",
              "June",
              "July",
              "August",
              "September",
              "October",
              "November",
              "December",
            ],
            day = dateInfo.getDate();

          // store date
          var currentDate =
            dow[dateInfo.getDay()] +
            ", " +
            month[dateInfo.getMonth()] +
            " " +
            day;

          document.getElementsByClassName("date")[0].innerHTML = currentDate;
        }

        /*  Add Date options */

        // print time and date once, then update them every second
        updateTime();
        setInterval(function () {
          updateTime();
        }, 1000);
      </script>

      <!-- CALENDAR -->
      <div class="box">
        <div class="container">
          <div id="calendar"></div>
        </div>
      </div>

      <script src="homepage_calendar.js"></script>
      <script src="{{asset('js/homepage_calendar.js')}}"></script>
      <script>
        dycalendar.draw({
          target: "#calendar",
          type: "month",
          dayformat: "full",
          monthformat: "full",
          highlighttargetdate: true,
          prevnextbutton: "show",
        });
      </script>
      <script>
        (function (i, s, o, g, r, a, m) {
          i["GoogleAnalyticsObject"] = r;
          (i[r] =
            i[r] ||
            function () {
              (i[r].q = i[r].q || []).push(arguments);
            }),
            (i[r].l = 1 * new Date());
          (a = s.createElement(o)), (m = s.getElementsByTagName(o)[0]);
          a.async = 1;
          a.src = g;
          m.parentNode.insertBefore(a, m);
        })(
          window,
          document,
          "script",
          "//www.google-analytics.com/analytics.js",
          "ga"
        );

        ga("create", "UA-46156385-1", "cssscript.com");
        ga("send", "pageview");
      </script>
    </div>
    <!-- WEATHER FORECAST -->
    <div class="weather_forcast_container">
      <h1>Weather Forecast</h1>
      <div class="weatherDiv">
        <a
          class="weatherwidget-io"
          href="https://forecast7.com/en/14d96120d79/apalit/"
          data-label_1="APALIT"
          data-label_2="WEATHER"
          data-font="Arial Black"
          data-icons="Climacons Animated"
          data-theme="clear"
          >APALIT WEATHER</a
        >
      </div>
      <script>
        !(function (d, s, id) {
          var js,
            fjs = d.getElementsByTagName(s)[0];
          if (!d.getElementById(id)) {
            js = d.createElement(s);
            js.id = id;
            js.src = "https://weatherwidget.io/js/widget.min.js";
            fjs.parentNode.insertBefore(js, fjs);
          }
        })(document, "script", "weatherwidget-io-js");
      </script>
    </div>
    <!-- ANNOUNCEMENT -->
    <!--
    <div>
      <div class="slideshow-container">
        <div class="announcement">ANNOUNCEMENT</div>
        <div class="mySlides">
          <div class="aligned">

            <img src="/images/homepage_images/lvcc.png" width="50" alt="lvcc.png" />

            <span>La Verdad Christian College - Apalit</span>
          </div>
          <p class="mySlides_content">
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque
            tenetur dolorum, rem voluptates velit necessitatibus, eaque
            provident cupiditate maxime ut saepe. Veniam id magnam, provident
            non ipsa accusamus aut iure architecto praesentium nobis possimus,
            eum odit illum temporibus placeat rerum fugiat officia ex magni
            recusandae corporis tempore.
          </p>
          <br />
          <p class="mySlides_content">
            Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quo
            commodi fugit placeat est enim sapiente, veniam assumenda minima non
            numquam soluta aperiam aut laboriosam harum, ut adipisci voluptatem?
            Natus fugit accusamus necessitatibus, aliquam et porro iure earum,
            consequatur repellat, beatae repudiandae error animi. Temporibus
            animi velit tenetur corporis odit doloribus!
          </p>
        </div>
        <div class="mySlides">
          <div class="aligned">
            <img src="/images/homepage_images/lvcc.png" width="50" alt="lvcc.png" />

            <span>La Verdad Christian College - Apalit</span>
          </div>
          <p class="mySlides_content">
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque
            tenetur dolorum, rem voluptates velit necessitatibus, eaque
            provident cupiditate maxime ut saepe.
          </p>
          <br />
          <p class="mySlides_content">
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Iure unde
            numquam dolorem consequuntur corporis vitae blanditiis omnis
            provident alias rerum libero itaque reiciendis non tenetur harum
            quibusdam mollitia, cupiditate placeat?
          </p>
          <br />
          <p class="mySlides_content">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam
            quaerat laboriosam reiciendis dolorum consectetur odio atque minus
            est animi aspernatur?
          </p>
        </div>
        <div class="mySlides">
          <div class="aligned">
            <img src="/images/homepage_images/lvcc.png" width="50" alt="lvcc.png" />

            <span>La Verdad Christian College - Apalit</span>
          </div>
          <p class="mySlides_content">
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque
            tenetur dolorum, rem voluptates velit necessitatibus, eaque
            provident cupiditate maxime ut saepe. Veniam id magnam, provident
            non ipsa accusamus aut iure architecto praesentium nobis possimus,
            eum odit illum temporibus placeat rerum fugiat officia ex magni
            recusandae corporis tempore.
          </p>
          <br />
          <p class="mySlides_content">
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
            Perspiciatis, molestias aliquid ex, labore totam obcaecati veritatis
            consequuntur natus similique odit distinctio illum quasi! Doloribus,
            consequatur nemo deleniti quos soluta itaque?
          </p>
        </div>

        <a class="slides_prev" onclick="plusSlides(-1)">❮</a>
        <a class="slides_next" onclick="plusSlides(1)">❯</a>
      </div>
    </div>

    <div class="dot-container">
      <span class="dot" onclick="currentSlide(1)"></span>
      <span class="dot" onclick="currentSlide(2)"></span>
      <span class="dot" onclick="currentSlide(3)"></span>
    </div>

    -->

    <script>
      var slideIndex = 1;
      showSlides(slideIndex);

      function plusSlides(n) {
        showSlides((slideIndex += n));
      }

      function currentSlide(n) {
        showSlides((slideIndex = n));
      }

      function showSlides(n) {
        var i;
        var slides = document.getElementsByClassName("mySlides");
        var dots = document.getElementsByClassName("dot");
        if (n > slides.length) {
          slideIndex = 1;
        }
        if (n < 1) {
          slideIndex = slides.length;
        }
        for (i = 0; i < slides.length; i++) {
          slides[i].style.display = "none";
        }
        for (i = 0; i < dots.length; i++) {
          dots[i].className = dots[i].className.replace(" active", "");
        }
        slides[slideIndex - 1].style.display = "block";
        dots[slideIndex - 1].className += " active";
      }
    </script>
    <!-- CHED NEWS -->
    <!--
    <div class="news_Container">
      <h1>CHED NEWS</h1>
      <div class="news_div_container">
        <div class="news_div">
          <div class="newscontent">
            <div class="news_image">
              <a href="#"><img src="/images/homepage_images/5.jpg" alt="news image" /></a>
            </div>
            <div class="news_description">
              <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit.
                Pariatur eaque magnam odio natus reiciendis, illo temporibus
                animi impedit, ut perferendis obcaecati tempore possimus
                excepturi totam blanditiis velit magni at reprehenderit quasi
                necessitatibus repudiandae. Voluptatem aspernatur quos
                voluptates fugit praesentium sunt dolore quibusdam inventore
                illo, reiciendis, aperiam, maiores dolorem quas unde!
              </p>
              <p><b class="p1">Posted on</b><b class="p2"> June 17, 2021</b></p>
            </div>
          </div>
          <div class="newscontent">
            <div class="news_image">
              <a href="#"><img src="/images/homepage_images/5.jpg" alt="news image"/></a>
            </div>
            <div class="news_description">
              <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit.
                Pariatur eaque magnam odio natus reiciendis, illo temporibus
                animi impedit, ut perferendis obcaecati tempore possimus
                excepturi totam blanditiis velit magni at reprehenderit quasi
                necessitatibus repudiandae. Voluptatem aspernatur quos
                voluptates fugit praesentium sunt dolore quibusdam inventore
                illo, reiciendis, aperiam, maiores dolorem quas unde!
              </p>
              <p><b class="p1">Posted on</b><b class="p2"> June 17, 2021</b></p>
            </div>
          </div>
        </div>
        <div class="news_div">
          <div class="newscontent">
            <div class="news_image">
              <a href="#"><img src="/images/homepage_images/5.jpg" alt="news image"/></a>
            </div>
            <div class="news_description">
              <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit.
                Pariatur eaque magnam odio natus reiciendis, illo temporibus
                animi impedit, ut perferendis obcaecati tempore possimus
                excepturi totam blanditiis velit magni at reprehenderit quasi
                necessitatibus repudiandae. Voluptatem aspernatur quos
                voluptates fugit praesentium sunt dolore quibusdam inventore
                illo, reiciendis, aperiam, maiores dolorem quas unde!
              </p>
              <p><b class="p1">Posted on</b><b class="p2"> June 17, 2021</b></p>
            </div>
          </div>
          <div class="newscontent">
            <div class="news_image">
              <a href="#"><img src="/images/homepage_images/5.jpg" alt="news image" /></a>
            </div>
            <div class="news_description">
              <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit.
                Pariatur eaque magnam odio natus reiciendis, illo temporibus
                animi impedit, ut perferendis obcaecati tempore possimus
                excepturi totam blanditiis velit magni at reprehenderit quasi
                necessitatibus repudiandae. Voluptatem aspernatur quos
                voluptates fugit praesentium sunt dolore quibusdam inventore
                illo, reiciendis, aperiam, maiores dolorem quas unde!
              </p>
              <p><b class="p1">Posted on</b><b class="p2"> June 17, 2021</b></p>
            </div>
          </div>
        </div>
      </div>
    </div>
    -->
    <footer>
      <p class="copyright">
        <b> Copyright &copy; 2021. All rights reserved </b>
      </p>
    </footer>
  </body>
</html>
