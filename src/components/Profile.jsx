import React, { useState, useEffect } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";

function Profile() {
  const navigate = useNavigate();
  const [userData, setUserData] = useState({
    email: "",
    firstName: "",
    lastName: "",
    userName: "",
    userRole: "",
  });
  const [passwordData, setPasswordData] = useState({
    currentPassword: "",
    newPassword: "",
    confirmNewPassword: "",
  });
  const [profileError, setProfileError] = useState("");
  const [passwordError, setPasswordError] = useState("");
  const [profileSuccessMessage, setProfileSuccessMessage] = useState("");
  const [passwordSuccessMessage, setPasswordSuccessMessage] = useState("");

  useEffect(() => {
    if (!localStorage.getItem("token")) {
      navigate("/login");
    }
    fetchUserData();
  }, []);

  const fetchUserData = async () => {
    const token = localStorage.getItem("token");
    try {
      const response = await axios.get(
        "http://localhost/ticketon/get_profile.php",
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );
      setUserData(response.data);
    } catch (error) {
      console.error("Failed to fetch user data", error);
    }
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setUserData((prevState) => ({
      ...prevState,
      [name]: value || "",
    }));
  };

  const handlePasswordChange = (e) => {
    const { name, value } = e.target;
    setPasswordData((prevState) => ({
      ...prevState,
      [name]: value || "",
    }));
  };

  const handleProfileSubmit = async (e) => {
    e.preventDefault();
    setProfileError("");
    setProfileSuccessMessage("");
    const token = localStorage.getItem("token");
    try {
      const response = await axios.post(
        "http://localhost/ticketon/update_profile.php",
        userData,
        {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        }
      );
      if (response.data.success) {
        setProfileSuccessMessage(response.data.success);
      } else {
        setProfileError(response.data.error);
      }
    } catch (error) {
      setProfileError("Failed to update profile");
    }
  };

  const validatePasswordForm = () => {
    const { newPassword, confirmNewPassword } = passwordData;
    if (newPassword !== confirmNewPassword) {
      setPasswordError("Passwords do not match");
      return false;
    }
    if (!/[A-Z]/.test(newPassword)) {
      setPasswordError(
        "Пароль должен содержать хотя бы одну букву в верхнем регистре."
      );
      return false;
    }
    if (!/[a-z]/.test(newPassword)) {
      setPasswordError(
        "Пароль должен содержать хотя бы одну букву в нижнем регистре."
      );
      return false;
    }
    if (!/\d/.test(newPassword)) {
      setPasswordError("Пароль должен содержать хотя бы одну цифру.");
      return false;
    }
    if (!/[!@#$%^&*()_+{}[\]:;<>,.?~\\/-]/.test(newPassword)) {
      setPasswordError(
        "Пароль должен содержать хотя бы один специальный символ."
      );
      return false;
    }
    if (newPassword.length < 8) {
      setPasswordError("Password must be at least 8 characters long");
      return false;
    }
    return true;
  };

  const handlePasswordSubmit = async (e) => {
    e.preventDefault();
    setPasswordError("");
    setPasswordSuccessMessage("");
    if (!validatePasswordForm()) return;
    try {
      const response = await axios.post(
        "http://localhost/ticketon/update_password.php",
        passwordData,
        {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            "Content-Type": "application/json",
          },
        }
      );
      if (response.data.success) {
        setPasswordSuccessMessage(response.data.success);
      } else {
        setPasswordError(response.data.error);
      }
    } catch (error) {
      setPasswordError("Failed to update password");
    }
  };

  const handleLogout = () => {
    localStorage.removeItem("token");
    navigate("/");
    window.location.reload();
  };

  const confirmDelete = async () => {
    if (
      window.confirm(
        "Are you sure you want to delete your account? This action cannot be undone."
      )
    ) {
      try {
        const response = await axios.post(
          "http://localhost/ticketon/delete_user.php",
          {},
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
          }
        );
        if (response.data.success) {
          handleLogout();
        } else {
          setProfileError(response.data.error);
        }
      } catch (error) {
        setProfileError("Failed to delete account");
      }
    }
  };

  return (
    <div className="container mt-5">
      <div className="d-flex justify-content-center">
        <div className="col-auto">
          <form className="form-signin" onSubmit={handleProfileSubmit}>
            <div className="form-floating mb-3">
              <input
                type="email"
                className="form-control"
                name="email"
                value={userData.email}
                readOnly
              />
              <label>Email:</label>
            </div>
            <div className="form-floating mb-3">
              <input
                type="text"
                className="form-control"
                name="firstName"
                value={userData.firstName}
                onChange={handleInputChange}
              />
              <label>First Name:</label>
            </div>
            <div className="form-floating mb-3">
              <input
                type="text"
                className="form-control"
                name="lastName"
                value={userData.lastName}
                onChange={handleInputChange}
              />
              <label>Last Name:</label>
            </div>
            <div className="form-floating mb-3">
              <input
                type="text"
                className="form-control"
                name="userName"
                value={userData.userName}
                onChange={handleInputChange}
              />
              <label>Username:</label>
            </div>
            <button type="submit" className="btn btn-warning w-100 mb-3">
              Update Profile
            </button>
            {profileSuccessMessage && (
              <div
                style={{ width: "208px" }}
                className="alert alert-success mt-3"
              >
                {profileSuccessMessage}
              </div>
            )}
            {profileError && (
              <div
                style={{ width: "208px" }}
                className="alert alert-danger mt-3"
              >
                {profileError}
              </div>
            )}
          </form>
        </div>
        <div className="col-auto">
          <form className="form-signin" onSubmit={handlePasswordSubmit}>
            <div className="form-floating mb-3">
              <input
                type="password"
                className="form-control"
                name="currentPassword"
                placeholder="Current Password"
                value={passwordData.currentPassword}
                onChange={handlePasswordChange}
              />
              <label>Current Password</label>
            </div>
            <div className="form-floating mb-3">
              <input
                type="password"
                className="form-control"
                name="newPassword"
                placeholder="New Password"
                value={passwordData.newPassword}
                onChange={handlePasswordChange}
              />
              <label>New Password</label>
            </div>
            <div className="form-floating mb-3">
              <input
                type="password"
                className="form-control"
                name="confirmNewPassword"
                placeholder="Confirm New Password"
                value={passwordData.confirmNewPassword}
                onChange={handlePasswordChange}
              />
              <label>Confirm New Password</label>
            </div>
            <button type="submit" className="btn btn-warning w-100 mb-3">
              Update Password
            </button>
            {passwordSuccessMessage && (
              <div
                style={{ width: "208px" }}
                className="alert alert-success mt-3"
              >
                {passwordSuccessMessage}
              </div>
            )}
            {passwordError && (
              <div
                style={{ width: "208px" }}
                className="alert alert-danger mt-3"
              >
                {passwordError}
              </div>
            )}
          </form>
          <button
            type="button"
            className="btn btn-danger w-100 mb-3"
            onClick={handleLogout}
          >
            Log out
          </button>
          <button
            type="button"
            className="btn btn-danger w-100"
            onClick={confirmDelete}
          >
            Delete Account
          </button>
        </div>
      </div>
    </div>
  );
}

export default Profile;
