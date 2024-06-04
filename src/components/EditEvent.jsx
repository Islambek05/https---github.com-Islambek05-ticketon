import React, { useState, useEffect } from "react";
import { useNavigate, useLocation } from "react-router-dom";
import axios from "axios";

function EditEvent() {
  const navigate = useNavigate();
  const location = useLocation();
  const eventID = new URLSearchParams(location.search).get("eventID");
  const [event, setEvent] = useState(null);
  const [error, setError] = useState("");

  useEffect(() => {
    if (!eventID) {
      setError("Event not specified");
      return;
    }
    fetchEvent(eventID);
  }, [eventID]);

  const fetchEvent = async (eventID) => {
    try {
      const response = await axios.get(
        `http://localhost/ticketon/getEvent.php?eventID=${eventID}`
      );
      setEvent(response.data);
    } catch (error) {
      setError("Failed to load event. " + error.message);
    }
  };

  const handleInputChange = (e) => {
    const { name, value, files } = e.target;
    setEvent((prev) => ({ ...prev, [name]: files ? files[0] : value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData();
    Object.keys(formData).forEach((key) => {
      formData.append(key, event[key]);
    });
    if (event.eventPoster) {
      formData.append("newEventPoster", event.eventPoster);
    }

    try {
      const response = await axios.post(
        `http://localhost/ticketon/editEvent.php`,
        formData,
        {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        }
      );
      if (response.data.success) {
        window.location.reload();
      } else {
        throw new Error(response.data.error || "Unknown error");
      }
    } catch (error) {
      console.error("Failed to edit event:", error);
      setError(
        `Failed to edit event. ${
          error.response ? error.response.data.error : error.message
        }`
      );
    }
  };

  const handleDeleteEvent = async (e) => {
    e.preventDefault();
    if (
      window.confirm(
        "Are you sure you want to delete this event? This action cannot be undone."
      )
    ) {
      try {
        await axios.delete(`http://localhost/ticketon/delete_event.php`, {
          data: { eventID },
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        navigate("/index");
      } catch (error) {
        setError("Failed to delete event. " + error.message);
      }
    }
  };

  if (error) return <div>Error: {error}</div>;
  if (!event) return <div>Loading...</div>;

  return (
    <div className="container mt-5">
      <h2>Edit Event: {event ? event.EventName : "Loading..."}</h2>
      <form onSubmit={handleSubmit} encType="multipart/form-data">
        <div className="mb-3">
          <label htmlFor="eventName" className="form-label">
            Event Name:
          </label>
          <input
            type="text"
            id="eventName"
            name="eventName"
            className="form-control"
            value={event.EventName}
            onChange={handleInputChange}
          />
        </div>

        {/* Event Date */}
        <div className="mb-3">
          <label htmlFor="eventDate" className="form-label">
            Event Date:
          </label>
          <input
            type="date"
            id="eventDate"
            name="eventDate"
            className="form-control"
            value={event.EventDate}
            onChange={handleInputChange}
            required
          />
        </div>

        {/* Event Time */}
        <div className="mb-3">
          <label htmlFor="eventTime" className="form-label">
            Event Time:
          </label>
          <input
            type="time"
            id="eventTime"
            name="eventTime"
            className="form-control"
            value={event.EventTime}
            onChange={handleInputChange}
            required
          />
        </div>

        {/* Tickets */}
        <div className="mb-3">
          <label htmlFor="tickets" className="form-label">
            Tickets Available:
          </label>
          <input
            type="number"
            id="tickets"
            name="tickets"
            className="form-control"
            value={event.Tickets}
            onChange={handleInputChange}
            required
          />
        </div>

        {/* Location */}
        <div className="mb-3">
          <label htmlFor="location" className="form-label">
            Location:
          </label>
          <input
            type="text"
            id="location"
            name="location"
            className="form-control"
            value={event.Location}
            onChange={handleInputChange}
            required
          />
        </div>

        {/* Description */}
        <div className="mb-3">
          <label htmlFor="description" className="form-label">
            Description:
          </label>
          <textarea
            id="description"
            name="description"
            className="form-control"
            value={event.Description}
            onChange={handleInputChange}
            required
          />
        </div>

        {/* Event Poster */}
        <div className="mb-3">
          <label htmlFor="eventPoster" className="form-label">
            Event Poster:
          </label>
          <br />
          {event.EventPoster && (
            <img
              src={`data:image/jpeg;base64,${event.EventPoster}`}
              alt={`${event.EventName} Poster`}
              className="card-img-info rounded"
            />
          )}
          <input
            type="file"
            id="eventPoster"
            name="eventPoster"
            onChange={handleInputChange}
          />
        </div>

        <button type="submit" className="btn btn-primary">
          Save Changes
        </button>
      </form>
      <form onSubmit={handleDeleteEvent}>
        <button type="submit" className="btn btn-danger">
          Delete Event
        </button>
      </form>
    </div>
  );
}

export default EditEvent;
