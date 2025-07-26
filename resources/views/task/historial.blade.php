<!doctype html>
<html lang="en">
    <head>
        <title>Ranking</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />

        <!-- Bootstrap CSS v5.2.1 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
    </head>

    <style>
        /* Haunted look styles */
        body {
          background-color: #181717; /* Blanco claro */
          background-image: linear-gradient(to bottom, #3d3b3b, #181717);
          background-size: cover;
          background-repeat: no-repeat;
          font-family: Arial, sans-serif;
          color: #333;
        }
        
  
        .card {
          border: 1px solid #cccccc;
          border-radius: 10px;
          box-shadow: 0 0 25px rgba(0, 0, 0, 0.2);
          background-color: #f5f5f5;
          padding: 15px;
        }
  
        .card-header {
          background-color: #ddd;
          padding: 10px 15px;
          border-radius: 5px 5px 0 0;
          font-weight: bold;
          color: #333;
          text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }
        main{
          background-color: #181717; /* Blanco claro */
          background-image: linear-gradient(to bottom, #3d3b3b, #181717);
        }
        
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
          color: #1a1a1a;
          text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }
  
        .table-primary {
          background-color: #f5f5f5;
          color: #333;
          text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }
  
        .table-primary th,
        .table-primary td {
          border-color: #ddd;
        }
  
        .table-primary tr:hover {
          background-color: #e9ecef;
          cursor: pointer;
        }
  
        .btn-primary {
          background-color: #993333;
          border-color: #993333;
          color: #fff;
          text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }
  
        .btn-primary:hover {
          background-color: #c25252;
          border-color: #c25252;
        }
  
        .alert-success,
        .alert-danger {
          background-color: rgba(255, 255, 255, 0.8);
          border-color: #ddd;
          color: #333;
          text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }
      </style>
    <body>
        <header>@include('task.header')</header>
        
        <br>
        <main class="container-fluid">
            @if (session('Success'))
                <div class="alert alert-success" role="alert">
                    {{session('Success')}}
                </div>
            @endif
            @if (session('Warning'))
                <div class="alert alert-danger" role="alert">
                    {{session('Warning')}}
                </div>
            @endif
            @if (session('exist'))
                <div class="alert alert-danger" role="alert">
                    {{session('exist')}}
                </div>
            @endif 
            <div class="card">
                <div class="card-header">
                    Últimos 10 Juegos
                    <button id="toggle-dark-mode" class="btn btn-dark float-end">Modo Oscuro</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive-sm">
                        @foreach($games as $i => $game)
                            <div class="historial-game" style="display: {{ $i === 0 ? 'block' : 'none' }};">
                                <h3>{{ $game['game'] }}</h3>
                                @foreach($game['participants'] as $bloqueIndex => $participantes)
                                    <div class="row">
                                        <!-- Ganadores -->
                                        <div class="col-md-6">
                                            <h5>Ganadores</h5>
                                            <table class="table table-success">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Invocador</th>
                                                        <th scope="col">Champion</th>
                                                        <th scope="col">KDA</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($participantes as $key => $participante)
                                                        @if($game['status'][$key]['win'])
                                                            <tr>
                                                                <td>{{ $game['status'][$key]['riotIdGameName'] }}</td>
                                                                <td>{{ $game['status'][$key]['champ'] }}</td>
                                                                <td>{{ $game['status'][$key]['kills'] }}/{{ $game['status'][$key]['deaths'] }}/{{ $game['status'][$key]['assists'] }}</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- Perdedores -->
                                        <div class="col-md-6">
                                            <h5>Perdedores</h5>
                                            <table class="table table-danger">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Invocador</th>
                                                        <th scope="col">Champion</th>
                                                        <th scope="col">KDA</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($participantes as $key => $participante)
                                                        @if(!$game['status'][$key]['win'])
                                                            <tr>
                                                                <td>{{ $game['status'][$key]['riotIdGameName'] }}</td>
                                                                <td>{{ $game['status'][$key]['champ'] }}</td>
                                                                <td>{{ $game['status'][$key]['kills'] }}/{{ $game['status'][$key]['deaths'] }}/{{ $game['status'][$key]['assists'] }}</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <!-- Botones de navegación -->
                        <div class="d-flex justify-content-between mt-3">
                            <button id="prevGame" class="btn btn-secondary" disabled>Anterior</button>
                            <button id="nextGame" class="btn btn-secondary">Siguiente</button>
                        </div>
                    </div>
                </div>
            </div>
        <br>
        </main>
        <footer>
            <!-- place footer here -->
        </footer>
        <!-- Bootstrap JavaScript Libraries -->
        <script>
            document.getElementById("toggle-dark-mode").addEventListener("click", function () {
                document.body.classList.toggle("dark-mode");
            });
        </script>
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous"
        ></script>
        <script>
            let currentGame = 0;
            const games = document.querySelectorAll('.historial-game');
            const prevBtn = document.getElementById('prevGame');
            const nextBtn = document.getElementById('nextGame');

            function showGame(index) {
                games.forEach((game, i) => {
                    game.style.display = (i === index) ? 'block' : 'none';
                });
                prevBtn.disabled = index === 0;
                nextBtn.disabled = index === games.length - 1;
            }

            prevBtn.addEventListener('click', function() {
                if (currentGame > 0) {
                    currentGame--;
                    showGame(currentGame);
                }
            });

            nextBtn.addEventListener('click', function() {
                if (currentGame < games.length - 1) {
                    currentGame++;
                    showGame(currentGame);
                }
            });

            // Inicializa la vista
            showGame(currentGame);
        </script>
    </body>
</html>

<style>
body.dark-mode {
    background-color: #0A1428;
    background-image: none;
    color: #E9C46A;
}
.dark-mode .card {
    background-color: #1C2333;
    color: #E9C46A;
    border-color: #2C394B;
}
.dark-mode .card-header {
    background-color: #2C394B;
    color: #E9C46A;
}
.dark-mode .table-success {
    background-color: #22304A !important;
    color: #E9C46A;
}
.dark-mode h5 {
    color: white;
}
.dark-mode h3 {
    color: white;
}

.dark-mode .table-danger {
    background-color: #2C1A1A !important;
    color: #E76F51;
}
.dark-mode .btn,
.dark-mode .btn-secondary {
    background-color: #005A82;
    border-color: #005A82;
    color: #E9C46A;
}
.dark-mode .btn:hover,
.dark-mode .btn-secondary:hover {
    background-color: #0A89C0;
    border-color: #0A89C0;
    color: #FFF;
}
.dark-mode .alert-success,
.dark-mode .alert-danger {
    background-color: #22304A;
    border-color: #2C394B;
    color: #E9C46A;
}
.dark-mode th, .dark-mode td {
    border-color: #2C394B !important;
}
</style>