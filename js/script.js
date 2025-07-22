// TOGGLE DELETE MODAL HANDLER SETUP
// selecting all the delete buttons in the table
const btnsDelete = document.querySelectorAll(".action.delete-btn");
// selecting the delete confirmation modal on the page
const modalEl = document.querySelector(".modal_overlay");
// selecting the delete form, cancel and delete buttons, hidden inputs that hold the album details in the delete confirmation modal
const formModalDelete = document.querySelector("#delete-form");
const btnModalCancel = document.querySelector(".modal-btn.cancel-btn");
const btnModalDelete = document.querySelector(".modal-btn.delete-btn");
const inputAlbumId = document.querySelector(".modal-form.album-id");
const inputAlbumTitle = document.querySelector(".modal-form.album-title");
const inputArtistName = document.querySelector(".modal-form.artist-name");

// function that handles the showing of the delete confirmation modal and the appropriate album details
const showDeleteModal = (e) => {
  e.preventDefault();
  // add a class of active to the delete confirmation modal for it to be displayed
  modalEl.classList.add("active");

  //   grabbing the album details from the dataset object to fill in the information on the delete modal so the user tracks what album is to be deleted
  const albumId = e.target.dataset.albumId;
  const albumTitle = e.target.dataset.albumTitle;
  const artistName = e.target.dataset.artistName;

  //   selecting the modal info span elements to show the info for the selected album
  document.querySelector(".modal-info.id").textContent = albumId;
  document.querySelector(".modal-info.title").textContent = albumTitle;
  document.querySelector(".modal-info.artist-name").textContent = artistName;

  // Inserting the information about the album into the hidden inputs for form submission and sending the delete post request
  inputAlbumId.value = albumId;
  inputAlbumTitle.value = albumTitle;
  inputArtistName.value = artistName;
};

// function that handles the closing of the delete confirmation modal
const closeDeleteModal = () => {
  // remove the active class from the delete confirmation modal
  modalEl.classList.remove("active");
};

// closing the delete confirmation modal on clicking the cancel button
btnModalCancel.addEventListener("click", closeDeleteModal);

// setup and event listener to handle clicking for each delete button on the table
btnsDelete.forEach((btn) => btn.addEventListener("click", showDeleteModal));

// setup if user confirms to delete album on the modal to display browser confirm modal
formModalDelete.addEventListener("submit", (e) => {
  e.preventDefault();

  // Retreiving the information from the hidden inputs to add to the browser confirm modal so the user tracks what album to delete
  const albumId = inputAlbumId.value;
  const albumTitle = inputAlbumTitle.value;
  const artistName = inputArtistName.value;

  // Ensuring that all details are available before accepting user confirmation to delete
  const isConfirmedDelete =
    albumId &&
    albumTitle &&
    artistName &&
    confirm(
      `Are you sure you want to delete Album ${albumId}: ${albumTitle} by ${artistName} and all its tracks?`
    );

  // Close the delete modal if user decides to cancel the delete process
  !isConfirmedDelete && closeDeleteModal();
  // Submit the delete form if user decides to go ahead and delete album
  isConfirmedDelete && e.target.submit();
});
