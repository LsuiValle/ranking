<!doctype html>
<html lang="en">
    <head>
        <title>Ranking</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <!-- Bootstrap CSS v5.2.1 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
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
        <main class="container">
            <div class="card">
                <div class="card-header">Champions</div><br>
                <form action="{{ url('/champs') }}" method="GET" class="mb-3">
                    <div class="input-group">
                        <input
                            type="text"
                            class="form-control"
                            name="search"
                            placeholder="Buscar por Nombre, tittle o Tags"
                            value="{{ request('search') }}"
                        />
                        <button class="btn btn-primary" type="submit">Buscar</button>
                        <a href="{{ url('/champs') }}" class="btn btn-secondary">Limpiar</a>
                    </div>
                </form>
                <div class="card-body">
                    <br>
                    <div class="table-responsive-sm">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Tittle</th>
                                    <th scope="col">Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($champs as $champ)
                                <tr>
                                    <td>{{ $champ->id }}</td>
                                    <td>{{ $champ->name }}</td>
                                    <td>{{ $champ->title }}</td>
                                    <td>{{ $champ->tags }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{ $champs->links() }}
                    </div>
                </div>
            </div>
        </main>
        <footer>
            <!-- place footer here -->
        </footer>
        <!-- Bootstrap JavaScript Libraries -->
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous">
        </script>
    </body>
</html>