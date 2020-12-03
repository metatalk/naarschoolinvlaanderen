<nav id="top-nav" class="navbar navbar-expand-lg navbar-light margin-medium" style="background: white;"><!-- Probleem overflow met .bg-white -->
  <a class="navbar-brand" href="#">Filters</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropdownPeriode" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Filter op periode
        </a>
        <div class="dropdown-menu" aria-labelledby="dropdownPeriode">
          <a class="dropdown-item" data-filter="periode" data-filtervalue="periode-1">Periode 1</a>
          <a class="dropdown-item" data-filter="periode" data-filtervalue="periode-2">Periode 2</a>
          <a class="dropdown-item" data-filter="periode" data-filtervalue="periode-3">Periode 3</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropdownPeriode" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Filter op onderwijsnet
        </a>
        <div class="dropdown-menu" aria-labelledby="dropdownPeriode">
          <a class="dropdown-item" data-filter="onderwijsnet" data-filtervalue="Provinciaal">Provinciaal onderwijs</a>
          <a class="dropdown-item" data-filter="onderwijsnet" data-filtervalue="Gemeentelijk">Gemeentelijk onderwijs</a>
        </div>
      </li>
    </ul>
    <div class="my-2 my-lg-0">
      <a id="filters-erase" class="inverse-underline">Wis filters</a>
    </div>
  </div>
</nav>
