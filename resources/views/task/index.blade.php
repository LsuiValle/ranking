<!doctype html>
<html lang="en">
  <head>
    <title>Haunted Ranking</title>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
      crossorigin="anonymous"
    />

    <style>
      /* Haunted look styles */
      body {
          background-color: #181717; /* Blanco claro */
        background-image: linear-gradient(to bottom, #3d3b3b, #181717);
        background-image: url("/images/spooky_background.jpg"); /* Replace with path to your background image */
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
      .diamond {
        background-color: #b3e5fc;
      }

      .emerald {
        background-color: #c8e6c9;
      }
    </style>
  </head>

  <body>
    <header>@include('task.header')</header>

    <br>
    <main class="container-fluid">
        @if (session('Success'))
            <div class="alert alert-success" role="alert">
            {{ session('Success') }}
            </div>
        @endif
        @if (session('Warning'))
            <div class="alert alert-danger" role="alert">
            {{ session('Warning')}}
            </div>
        @endif
        @if (session('exist'))
            <div class="alert alert-danger" role="alert">
                {{session('exist')}}
            </div>
        @endif            
        <div class="card"> 
            <div class="card-header">Nuevo Competidor</div>
            <div class="card-body">
                <form action="{{url('/')}}" method="POST">
                    @csrf
                    <p>Ingresar Invocador</p>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Name Invocador" name="name" id="name" aria-label="Username" required>
                        <span class="input-group-text">#</span>
                        <input type="text" class="form-control" placeholder="Tag" name="tag" id="tag" aria-label="Server" required>
                        </div>
                    <input type="submit" class="btn btn-primary" value="Agregar Invocador">
                </form>
                <br><hr><br>
                <form action="{{ url('/') }}" method="GET" class="mb-3">
                    <div class="input-group">
                      <input
                        id="search-box"
                        type="text"
                        class="form-control"
                        name="search"
                        placeholder="Buscar por Nombre, División o Puntos"
                        value="{{ request('search') }}"
                      />
                        <button class="btn btn-primary" type="submit">Buscar</button>
                        <a href="{{ url('/') }}" class="btn btn-secondary">Limpiar</a>
                    </div>
                </form>
                <!-- Agrega este botón arriba de la tabla/lista de invocadores -->
                <button id="updateAllBtn" class="btn btn-primary">Actualizar Invocadores</button>
                <div
                    class="table-responsive-sm">
                    <table
                        class="table table-primary">
                        <thead>
                            <tr>
                                <th scope="col">Position</th>
                                <th scope="col">Nombre</th>
                                <th scope="col" style="width: 10%;">Tag</th>
                                <th scope="col">Division</th>
                                <th scope="col">Rango</th>
                                <th scope="col">Puntos</th>
                            </tr>
                        </thead>
                        <tbody>
                          @foreach($tasks as $task)
                              @if($task->activo == 1)
                                  <tr class="{{ $loop->first ? 'table-warning' : '' }} {{ strtolower($task->division) }}">
                                      <td>#{{ $loop->iteration }}</td>
                                      <td>
                                          <a href="{{ route('historial.storeSession', ['id_user' => $task->id]) }}" style="color: black; text-decoration: none; cursor: pointer;">
                                             <img src="https://ddragon.leagueoflegends.com/cdn/14.24.1/img/profileicon/{{$task->icon_id}}.png" alt="" width="30px" height="30px"> {{ $task->game_name }}
                                          </a>
                                      </td>
                                      <td>
                                          <a href="{{ route('historial.storeSession', ['id_user' => $task->id]) }}" style="color: black; text-decoration: none; cursor: pointer;">
                                              {{ $task->tag_line }}
                                          </a>
                                      </td>
                                      <td>{{ $task->division }}</td>
                                      <td>{{ $task->rango }}</td>
                                      <td>{{ $task->points }}</td>
                                  </tr>
                              @endif
                          @endforeach
                      </tbody>                      
                    </table>
                </div>
                <div>
                </div>
                @if($tasks->isEmpty())
                  <tr>
                      <td colspan="7" class="text-center">No se encontraron resultados.</td>
                  </tr>
                @endif
            </div>
        </div>
        <br>
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
        <script>
          function debounce(func, wait) {
            let timeout;
            return function (...args) {
              const later = () => {
                clearTimeout(timeout);
                func(...args);
              };
              clearTimeout(timeout);
              timeout = setTimeout(later, wait);
            };
          }
          document.getElementById('search-box').addEventListener('input', function () {
            const searchValue = this.value;

            // Enviar la solicitud AJAX al servidor
            fetch(`/?search=${encodeURIComponent(searchValue)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.querySelector('tbody');
                    tableBody.innerHTML = '';

                    // Actualizar la tabla con los datos recibidos
                    if (data.tasks.length > 0) {
                        data.tasks.forEach((task) => {
                            const row = `
                                <tr>
                                    <td>${task.level || '-'}</td>
                                    <td><img src="https://ddragon.leagueoflegends.com/cdn/14.24.1/img/profileicon/${task.icon_id}.png" alt="" width="30px" height="30px">${task.game_name || '-'}</td>
                                    <td>${task.tag_line || '-'}</td>
                                    <td>${task.division || '-'}</td>
                                    <td>${task.rango || '-'}</td>
                                    <td>${task.points || '-'}</td>
                                </tr>
                            `;
                            tableBody.insertAdjacentHTML('beforeend', row);
                        });
                    } else {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="6" class="text-center">No se encontraron resultados.</td>
                            </tr>
                        `;
                    }
                })
                .catch(error => console.error('Error al realizar la búsqueda:', error));
          });
        </script>        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
        $('#updateAllBtn').click(function() {
            $(this).prop('disabled', true).text('Actualizando...');
            $.ajax({
                url: '{{ route("tasks.updateAll") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Invocadores actualizados correctamente');
                    location.reload();
                },
                error: function() {
                    alert('Error al actualizar los invocadores');
                },
                complete: function() {
                    $('#updateAllBtn').prop('disabled', false).text('Actualizar Invocadores');
                }
            });
        });
        </script>
    </body>
</html>