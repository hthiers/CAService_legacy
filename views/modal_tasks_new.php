<!-- modal nuevo cliente -->
<div class="reveal" id="modalNuevoCliente" data-reveal>
  <h1>Agregar cliente <span class='fi-address-book'></span></h1>
  <form action="?controller=customers&amp;action=ajaxCustomersAdd" method="POST">
      <label for="name">Título cliente</label>
      <input type="text" name="dlgSbm_name_customer" id="dlgSbm_name_customer" />
      <label for="email">Descripción cliente</label>
      <input type="text" name="dlgSbm_desc_customer" id="dlgSbm_desc_customer" />

      <button class="button" type="submit">Guardar</button>
  </form>
</div>

<!-- modal nueva materia -->
<div class="reveal" id="modalNuevaMateria" data-reveal>
  <h1>Agregar materia <span class='fi-pricetag-multiple'></span></h1>
  <form action="?controller=types&amp;action=ajaxTypesAdd" method="POST">
    <label for="label_type">Nombre materia</label>
    <input type="text" name="dlgSbm_name_type" id="dlgSbm_name_type" />
    <button class="button" type="submit">Guardar</button>
  </form>
</div>

<!-- modal nueva gestion -->
<div class="reveal" id="modalNuevaGestion" data-reveal>
  <h1>Agregar gestión <span class='fi-list-bullet'></span></h1>
  <form action="?controller=managements&amp;action=ajaxManagementsAdd" method="POST">
    <label for="label_management">Nombre Gestión</label>
    <input type="text" name="dlgSbm_name_management" id="dlgSbm_name_management" />
    <button class="button" type="submit">Guardar</button>
  </form>
</div>

<!-- modal mensaje -->
<div class="reveal" id="modalMensaje" data-reveal>
  <h1 id="title_message"></h1>
  <p id="detail_message"> </p>
    <button class="button close_message close-reveal-modal">Cerrar</button>
</div>
